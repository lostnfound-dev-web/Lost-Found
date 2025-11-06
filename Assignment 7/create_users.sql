CREATE TABLE IF NOT EXISTS Users (
  username VARCHAR(50) PRIMARY KEY,
  password VARCHAR(255)
);

INSERT INTO Users (username, password)
VALUES ('admin.uni@uni.de', 'Adm#Uni2025!')
ON DUPLICATE KEY UPDATE password = 'Adm#Uni2025!';