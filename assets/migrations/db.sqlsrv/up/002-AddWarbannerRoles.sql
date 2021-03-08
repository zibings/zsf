BEGIN TRY
	BEGIN TRANSACTION;

		INSERT INTO [Role] ([Name], [Created]) VALUES ('Moderator', GETUTCDATE());
		INSERT INTO [Role] ([Name], [Created]) VALUES ('Warlord', GETUTCDATE());
		INSERT INTO [Role] ([Name], [Created]) VALUES ('Bannerman', GETUTCDATE());

	COMMIT
END TRY
BEGIN CATCH
	PRINT 'There was an error in the script, rolling back'
	ROLLBACK

	SELECT ERROR_NUMBER() AS ErrorNumber, ERROR_LINE() AS ErrorLine, ERROR_MESSAGE() AS ErrorMessage
END CATCH