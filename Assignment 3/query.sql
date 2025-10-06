--Member 1 
-- 1) Classify users as Student/Admin with emails
SELECT
  u.UserID,
  u.Email,
  CASE
    WHEN a.UserID IS NOT NULL THEN 'Admin'
    WHEN s.UserID IS NOT NULL THEN 'Student'
    ELSE 'Unknown'
  END AS Role
FROM User u
LEFT JOIN Student s ON s.UserID = u.UserID
LEFT JOIN Admin   a ON a.UserID = u.UserID
ORDER BY Role, u.Email;

-- 2) Total report counts per student
SELECT u.Email AS student_email, COUNT(*) AS total_reports
FROM Report r
JOIN Student s ON s.UserID = r.UserID
JOIN User u    ON u.UserID = s.UserID
GROUP BY u.Email
ORDER BY total_reports DESC, u.Email;

-- 3) Show activity of each user
SELECT
  u.UserID,
  u.Email,
  'Student' AS Role,
  CASE
    WHEN COUNT(r.ReportID) >= 2 THEN 'Active Reporter'
    ELSE 'Inactive Reporter'
  END AS Activity
FROM User u
JOIN Student s ON s.UserID = u.UserID
LEFT JOIN Report r ON r.UserID = u.UserID
GROUP BY u.UserID, u.Email
ORDER BY Activity DESC, u.Email;

--Member 2
-- 4) Items reported lost
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  i.Category
FROM Item i
JOIN LostItem li ON li.ItemID = i.ItemID
ORDER BY i.ItemID;

-- 5) Dates items were lost
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  i.DateLost
FROM Item i
WHERE i.DateLost IS NOT NULL
ORDER BY i.DateLost;

-- 6) Locations where items were lost 
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  i.LocationLost
FROM Item i
WHERE i.LocationLost IS NOT NULL AND i.LocationLost <> ''
ORDER BY i.LocationLost, i.Name;

--Member 3
-- 7) Pending status with the items
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  i.Category,
  s.Label AS status
FROM Item i
JOIN ItemStatus ist ON ist.ItemID = i.ItemID
JOIN Status s       ON s.StatusID = ist.StatusID
WHERE s.Label = 'Pending'
ORDER BY i.ItemID;

-- 8) Accepted status with the items
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  i.Category,
  s.Label AS status
FROM Item i
JOIN ItemStatus ist ON ist.ItemID = i.ItemID
JOIN Status s       ON s.StatusID = ist.StatusID
WHERE s.Label = 'Accepted'
ORDER BY i.ItemID;

-- 9) Who did not report (students with zero reports)
SELECT u.Email AS student_email
FROM Student s
JOIN User u ON u.UserID = s.UserID
LEFT JOIN Report r ON r.UserID = s.UserID
GROUP BY u.Email
HAVING COUNT(r.ReportID) = 0
ORDER BY u.Email;

--Member 4
-- 10) Items whose verification status changed over time
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  MIN(s.Label) AS first_status,
  MAX(s.Label) AS latest_status
FROM Item i
JOIN AdminVerifiesItemStatus av ON av.ItemID = i.ItemID
JOIN Status s ON s.StatusID = av.StatusID
GROUP BY i.ItemID, i.Name
HAVING COUNT(DISTINCT s.Label) > 1
ORDER BY i.ItemID;

-- 11) Time of verifications
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  av.VerifiedAt
FROM AdminVerifiesItemStatus av
JOIN Item i ON i.ItemID = av.ItemID
ORDER BY av.VerifiedAt DESC, i.ItemID;

-- 12) Is the item found or not 
SELECT
  CONCAT('ITM-', LPAD(i.ItemID, 5, '0')) AS ItemID,
  i.Name,
  CASE
    WHEN EXISTS (
      SELECT 1
      FROM ReportItem rif
      JOIN FoundReport fr ON fr.ReportID = rif.ReportID
      WHERE rif.ItemID = i.ItemID
    )
    THEN 'Found'
    ELSE 'Not Found'
  END AS found_status
FROM Item i
ORDER BY i.ItemID;

-- Note: 
--Member 1 (Keid Mersini) : User-focused (classify users as Student/Admin, list their emails and show reporting activity)
--Member 2 (Kiara Mersini): Item-focused (list lost items with category, date and location details)
--Member 3 (Lend Kalemasi): Status-focused (show items with Pending/Accepted status and students with no reports)
--Member 4 (Anxhela Mata) : Verification-focused (track status changes, admin verifications and whether items are found)