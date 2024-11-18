/*

	Attempts to create the following tables:
		- User: Base account data for individual users
		- LoginKey: Login keys, 1+ for all users that can login
		- Role: Roles defined in system
		- UserRole: Entries that show which roles to which a user belongs
		- UserSession: User session storage for access control
		- UserAuthHistory: Historical auth-related actions for users (doesn't include data, merely audit trail)
		- UserToken: User token storage (for emails/etc) w/ text field for data context
		- UserProfile: Profile information for users
		- UserSettings: Settings data for users
		- UserVisibilities: Basic visibility settings for user information
		- UserContact: Collection of contact data for users
		- UserRelation: Table with mirror values for user relationships (friend -> family -> bestie? -> dating?), mirrored stage == accepted stage ('invite' means stages !=)
		- UserRelationEvent: Table to track actions related to user relationship changes

*/
CREATE OR REPLACE FUNCTION installBaseTables() RETURNS void AS $$
BEGIN
	CREATE TABLE IF NOT EXISTS "User" (
			"ID" SERIAL PRIMARY KEY,
			"Email" VARCHAR(256) NOT NULL,
			"EmailConfirmed" SMALLINT NOT NULL,
			"Joined" TIMESTAMP NOT NULL,
			"LastLogin" TIMESTAMP NULL,
			"LastActive" TIMESTAMP NULL
	);

	CREATE TABLE IF NOT EXISTS "LoginKey" (
			"UserID" INT NOT NULL,
			"Provider" SMALLINT NOT NULL,
			"Key" VARCHAR(1024) NOT NULL,
			CONSTRAINT "FK_LoginKeyUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "Role" (
			"ID" SERIAL PRIMARY KEY,
			"Name" VARCHAR(128) NOT NULL,
			"Created" TIMESTAMP NOT NULL
	);

	CREATE TABLE IF NOT EXISTS "UserRole" (
			"UserID" INT NOT NULL,
			"RoleID" INT NOT NULL,
			CONSTRAINT "UQ_UserRole" UNIQUE("UserID", "RoleID"),
			CONSTRAINT "FK_UserRoleUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE,
			CONSTRAINT "FK_UserRoleRole" FOREIGN KEY ("RoleID") REFERENCES "Role" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE "UserSession" (
			"ID" SERIAL PRIMARY KEY,
			"UserID" INT NOT NULL,
			"Created" TIMESTAMP NOT NULL,
			"Token" VARCHAR(256) NOT NULL,
			"Address" VARCHAR(128) NOT NULL,
			"Hostname" VARCHAR(512) NOT NULL,
			CONSTRAINT "FK_UserSessionUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserAuthHistory" (
			"UserID" INT NOT NULL,
			"Recorded" TIMESTAMP NOT NULL,
			"Action" SMALLINT NOT NULL,
			"Address" VARCHAR(128) NOT NULL,
			"Hostname" VARCHAR(512) NOT NULL,
			"Notes" VARCHAR(512) NOT NULL
	);

	CREATE TABLE IF NOT EXISTS "UserToken" (
			"ID" SERIAL PRIMARY KEY,
			"UserID" INT NOT NULL,
			"Created" TIMESTAMP NOT NULL,
			"Context" VARCHAR(4000) NOT NULL,
			"Token" VARCHAR(256) NOT NULL,
			CONSTRAINT "FK_UserTokenUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserProfile" (
			"UserID" INT NOT NULL,
			"DisplayName" VARCHAR(128) NOT NULL,
			"Birthday" TIMESTAMP NULL,
			"RealName" VARCHAR(128) NULL,
			"Description" VARCHAR(4000) NULL,
			"Gender" SMALLINT NULL,
			PRIMARY KEY ("UserID"),
			UNIQUE ("DisplayName"),
			CONSTRAINT "FK_UserProfileUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserSettings" (
			"UserID" INT NOT NULL,
			"HtmlEmails" SMALLINT NOT NULL,
			"PlaySounds" SMALLINT NOT NULL,
			PRIMARY KEY ("UserID"),
			CONSTRAINT "FK_UserSettingsUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserVisibilities" (
			"UserID" INT NOT NULL,
			"Profile" SMALLINT NOT NULL,
			"Email" SMALLINT NOT NULL,
			"Searches" SMALLINT NOT NULL,
			"Birthday" SMALLINT NOT NULL,
			"RealName" SMALLINT NOT NULL,
			"Description" SMALLINT NOT NULL,
			"Gender" SMALLINT NOT NULL,
			PRIMARY KEY ("UserID"),
			CONSTRAINT "FK_UserVisibilitiesUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserContact" (
			"UserID" INT NOT NULL,
			"Created" TIMESTAMP NOT NULL,
			"Type" SMALLINT NOT NULL,
			"Value" VARCHAR(512) NOT NULL,
			"Primary" SMALLINT NOT NULL,
			PRIMARY KEY ("UserID"),
			CONSTRAINT "FK_UserContactUser" FOREIGN KEY ("UserID") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserRelation" (
			"UserID_One" INT NOT NULL,
			"UserID_Two" INT NOT NULL,
			"Created" TIMESTAMP NOT NULL,
			"Stage" SMALLINT NOT NULL,
			"Origin" SMALLINT NOT NULL,
			PRIMARY KEY ("UserID_One", "UserID_Two"),
			CONSTRAINT "FK_UserRelationUser1" FOREIGN KEY ("UserID_One") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE,
			CONSTRAINT "FK_UserRelationUser2" FOREIGN KEY ("UserID_Two") REFERENCES "User" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
	);

	CREATE TABLE IF NOT EXISTS "UserRelationEvent" (
			"UserID_One" INT NOT NULL,
			"UserID_Two" INT NOT NULL,
			"Recorded" TIMESTAMP NOT NULL,
			"Stage" SMALLINT NOT NULL,
			"Action" SMALLINT NOT NULL,
			"Notes" VARCHAR(512) NOT NULL
	);
END;
$$ LANGUAGE plpgsql;