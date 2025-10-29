-- Seed data for Lost & Found project
USE db_anmata;

-- Insert Items
INSERT INTO Item (ItemID, Name, Category, Description, DateLost, LocationLost) VALUES
(20001,'Black Backpack','Bags','Medium-size backpack with patches','2025-09-28','Library'),
(20002,'Blue Jacket','Clothing','Winter jacket with hood','2025-10-01','Cafeteria'),
(20003,'iPhone 12','Electronics','Blue case, cracked screen','2025-10-02','Lecture Hall A'),
(20004,'Keys','Accessories','Bunch of 3 keys with red keychain','2025-10-03','Gym'),
(20005,'Wallet','Accessories','Brown leather wallet','2025-10-04','Dorm Lobby'),
(20006,'Laptop Charger','Electronics','Dell 65W charger','2025-10-05','Library'),
(20007,'Umbrella','Accessories','Black umbrella with wooden handle','2025-10-06','Bus Stop'),
(20008,'Headphones','Electronics','Sony WH-1000XM4, black','2025-10-07','Cafeteria'),
(20009,'Textbook','Books','Algorithms book, CLRS edition','2025-10-08','Lecture Hall B'),
(20010,'Student ID Card','Documents','Mercator College ID card','2025-10-09','Main Gate');

-- Insert corresponding LostItem entries
INSERT INTO LostItem (ItemID) VALUES
(20001),(20002),(20003),(20004),(20005),
(20006),(20007),(20008),(20009),(20010);
