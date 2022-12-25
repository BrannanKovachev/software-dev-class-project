-- 1
-- Displays the id of user registered to the email inputted. Use this to determine your Admin/Core account's ID
SELECT idusers FROM users where email = "example@gmail.com";

-- 2
-- Alternatively, if you would like to view all of the accounts, the following command will display the ID, name, and email of all accounts.
SELECT idusers, name, email FROM users;

-- 3
-- Updates the Account Type of the User with the inputted ID.
UPDATE users SET userType = # WHERE idusers = #;

    -- Account Types
        -- 0 = Normal User/Requester
        -- 1 = Driver
        -- 2 = Admin

    -- To reiterate, both '#' above should be replaced with relevant values.
    -- The first should be an integer that represents an Account Type as described above.
    -- The second should be a user's account ID.

-- 4
-- CORE ACCOUNTS
    -- Core Accounts cannot be Deleted or have their status changed without direct Database Access

    -- Sets the Account as a 'Core' Account
    UPDATE users SET idusers = # WHERE idusers = #;

        -- The first '#' MUST be replaced with a NEGATIVE INTEGER for the Account to registered as a Core Account. It must also not be another already existing user ID.
            -- You can view all user ID's with command number 2.
        -- As before, the second '#' should be a user's account ID.