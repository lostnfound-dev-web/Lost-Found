SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS AdminVerifiesItemStatus;
DROP TABLE IF EXISTS ItemStatus;
DROP TABLE IF EXISTS ReportItem;
DROP TABLE IF EXISTS Accepted;
DROP TABLE IF EXISTS Pending;
DROP TABLE IF EXISTS Status;
DROP TABLE IF EXISTS FoundItem;
DROP TABLE IF EXISTS LostItem;
DROP TABLE IF EXISTS Item;
DROP TABLE IF EXISTS FoundReport;
DROP TABLE IF EXISTS LostReport;
DROP TABLE IF EXISTS Report;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Student;
DROP TABLE IF EXISTS User;
SET FOREIGN_KEY_CHECKS = 1;

-- 1) User + ISA
CREATE TABLE User (
  UserID   INT AUTO_INCREMENT PRIMARY KEY,
  Email    VARCHAR(255) NOT NULL UNIQUE,
  Password VARCHAR(255) NOT NULL
);

CREATE TABLE Student (
  UserID INT PRIMARY KEY,
  FOREIGN KEY (UserID) REFERENCES User(UserID)
);

CREATE TABLE Admin (
  UserID INT PRIMARY KEY,
  FOREIGN KEY (UserID) REFERENCES User(UserID)
);

-- 2) Report + ISA
CREATE TABLE Report (
  ReportID   INT AUTO_INCREMENT PRIMARY KEY,
  ReportDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserID     INT NOT NULL,
  FOREIGN KEY (UserID) REFERENCES Student(UserID)
);

CREATE TABLE LostReport (
  ReportID INT PRIMARY KEY,
  FOREIGN KEY (ReportID) REFERENCES Report(ReportID)
);

CREATE TABLE FoundReport (
  ReportID INT PRIMARY KEY,
  FOREIGN KEY (ReportID) REFERENCES Report(ReportID)
);

-- 3) Item + ISA
CREATE TABLE Item (
  ItemID       INT AUTO_INCREMENT PRIMARY KEY,
  Name         VARCHAR(255) NOT NULL,
  Category     VARCHAR(100) NOT NULL,
  Description  TEXT,
  Photo        VARCHAR(255),
  DateLost     DATE,
  LocationLost VARCHAR(255)
);

CREATE TABLE LostItem (
  ItemID INT PRIMARY KEY,
  FOREIGN KEY (ItemID) REFERENCES Item(ItemID)
);

CREATE TABLE FoundItem (
  ItemID INT PRIMARY KEY,
  FOREIGN KEY (ItemID) REFERENCES Item(ItemID)
);

-- 4) Status + ISA
CREATE TABLE Status (
  StatusID INT AUTO_INCREMENT PRIMARY KEY,
  Label    VARCHAR(32) NOT NULL UNIQUE
);

CREATE TABLE Pending (
  StatusID INT PRIMARY KEY,
  FOREIGN KEY (StatusID) REFERENCES Status(StatusID)
);

CREATE TABLE Accepted (
  StatusID INT PRIMARY KEY,
  FOREIGN KEY (StatusID) REFERENCES Status(StatusID)
);

INSERT INTO Status(Label) VALUES ('Pending'), ('Accepted');

-- 5) Relationships
CREATE TABLE ReportItem (
  ReportID INT PRIMARY KEY,
  ItemID   INT NOT NULL,
  FOREIGN KEY (ReportID) REFERENCES Report(ReportID),
  FOREIGN KEY (ItemID)   REFERENCES Item(ItemID)
);

CREATE TABLE ItemStatus (
  ItemID   INT PRIMARY KEY,
  StatusID INT NOT NULL,
  FOREIGN KEY (ItemID)   REFERENCES Item(ItemID),
  FOREIGN KEY (StatusID) REFERENCES Status(StatusID)
);

CREATE TABLE AdminVerifiesItemStatus (
  AdminID    INT NOT NULL,
  ItemID     INT NOT NULL,
  StatusID   INT NOT NULL,
  VerifiedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (AdminID, ItemID, VerifiedAt),
  FOREIGN KEY (AdminID)  REFERENCES Admin(UserID),
  FOREIGN KEY (ItemID)   REFERENCES Item(ItemID),
  FOREIGN KEY (StatusID) REFERENCES Status(StatusID)
);

-- Users 
INSERT INTO User (UserID, Email, Password) VALUES
 (1001, 'luna.hoxha@uni.de',     'LuH!2025#'),
 (1002, 'marco.bianchi@uni.de',  'Mb_97*Uni'),
 (1003, 'sofia.martinez@uni.de', 'Sm@21Safe'),
 (1004, 'amelie.dupont@uni.de',  'Ad#2025!'),
 (1005, 'noah.johansson@uni.de', 'Nj$2025'),
 (1006, 'mei.yung@uni.de',       'My?2025'),   -- no reports
 (9001, 'admin.uni@uni.de',      'Adm#Uni2025!');

-- Roles
INSERT INTO Student (UserID) VALUES (1001),(1002),(1003),(1004),(1005),(1006);
INSERT INTO Admin   (UserID) VALUES (9001);

-- Reports & Items
-- 1) Luna -> Backpack
INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (101, '2025-09-10 09:15:00', 1001);
INSERT INTO Item   (ItemID, Name, Category, Description, Photo, DateLost,  LocationLost)
VALUES             (201, 'Backpack', 'Bags', 'Black Eastpak', 'backpack.png', '2025-09-10', 'Library');
INSERT INTO LostItem (ItemID) VALUES (201);
INSERT INTO ReportItem (ReportID, ItemID) VALUES (101, 201);

-- 2) Marco -> Phone 
INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (102, '2025-09-12 14:45:00', 1002);
INSERT INTO Item   (ItemID, Name, Category, Description, Photo, DateLost,  LocationLost)
VALUES             (202, 'Phone', 'Electronics', 'iPhone 13', 'phone.png', '2025-09-12', 'Gym');
INSERT INTO LostItem (ItemID) VALUES (202);
INSERT INTO ReportItem (ReportID, ItemID) VALUES (102, 202);

-- 3) Sofia -> Jacket 
INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (103, '2025-09-14 19:30:00', 1003);
INSERT INTO Item   (ItemID, Name, Category, Description, Photo, DateLost,  LocationLost)
VALUES             (203, 'Jacket', 'Clothes', 'Blue Adidas jacket', 'jacket.png', '2025-09-14', 'Cafeteria');
INSERT INTO LostItem (ItemID) VALUES (203);
INSERT INTO ReportItem (ReportID, ItemID) VALUES (103, 203);

-- 4) Amelie -> Laptop 
INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (104, '2025-09-13 11:20:00', 1004);
INSERT INTO Item   (ItemID, Name, Category, Description, Photo, DateLost,  LocationLost)
VALUES             (204, 'Laptop', 'Electronics', 'ThinkPad T14', 'laptop.png', '2025-09-13', 'Lecture Hall A');
INSERT INTO LostItem (ItemID) VALUES (204);
INSERT INTO ReportItem (ReportID, ItemID) VALUES (104, 204);

-- 5) Noah -> Tablet 
INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (105, '2025-09-15 17:10:00', 1005);
INSERT INTO Item   (ItemID, Name, Category, Description, Photo, DateLost,  LocationLost)
VALUES             (205, 'Tablet', 'Electronics', 'iPad Air', 'tablet.png', '2025-09-15', 'Library');
INSERT INTO LostItem (ItemID) VALUES (205);
INSERT INTO ReportItem (ReportID, ItemID) VALUES (105, 205);

INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (106, '2025-09-16 10:00:00', 1005);
INSERT INTO FoundReport (ReportID) VALUES (106);
INSERT INTO ReportItem  (ReportID, ItemID) VALUES (106, 201);

INSERT INTO Report (ReportID, ReportDate, UserID) VALUES (107, '2025-09-16 16:30:00', 1004);
INSERT INTO FoundReport (ReportID) VALUES (107);
INSERT INTO ReportItem  (ReportID, ItemID) VALUES (107, 202);

-- Current status 
INSERT INTO ItemStatus (ItemID, StatusID) VALUES
 (201, 2),
 (202, 2),
 (203, 1),
 (204, 1),
 (205, 1);

-- Verification history 
INSERT INTO AdminVerifiesItemStatus (AdminID, ItemID, StatusID, VerifiedAt) VALUES
 (9001, 201, 1, '2025-09-10 12:00:00'),
 (9001, 201, 2, '2025-09-16 10:30:00');
INSERT INTO AdminVerifiesItemStatus (AdminID, ItemID, StatusID, VerifiedAt) VALUES
 (9001, 202, 2, '2025-09-16 16:45:00');
INSERT INTO AdminVerifiesItemStatus (AdminID, ItemID, StatusID, VerifiedAt) VALUES
 (9001, 203, 1, '2025-09-14 20:00:00'),
 (9001, 204, 1, '2025-09-13 12:00:00'),
 (9001, 205, 1, '2025-09-15 18:00:00');