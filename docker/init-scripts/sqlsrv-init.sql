IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'zsf')
BEGIN
  CREATE DATABASE zsf;
END

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'zsf_test')
BEGIN
	CREATE DATABASE zsf_test;
END
