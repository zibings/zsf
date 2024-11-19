IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'zsf')
BEGIN
    CREATE DATABASE zsf;
END
