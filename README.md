Personal User Notes
=========================
This is a 3rd-party extension for phpBB 3.1 that gives users the ability to create prive notes. These are only viewable by the creator and can be accessed within the User Control Panel (UCP). Notes are stored and displayed exactly like actual posts, and can even be converted to a new topic in postable forums in which the user has posting permissions.

Requirements
=======
- >= phpBB 3.1-dev
- >= PHP 5.4

Testing
=======
Currently there is nothing to test, but when there is, anyone is welcome to aid with testing. If you encounter a problem, please create a new issue so that I can fix it. To test, you will need to do the following:

1. Checkout the latest version of the `develop` branch of @phpbb\phpbb3
2. Optionally merge in the latest version of @phpbb\phpbb3#1180 (Migrations PR)
3. Checkout the latest version of the `develop` branch of *this* repository and place it directly into the `phpbb/phpBB/ext/` directory (you will need to create it if you have not yet installed other extensions)
4. If you did not skip step 2, you should be able to just skip this step as well. Otherwise, run the SQL in this file: https://github.com/imkingdavid/pun/blob/57e45e72df07b7f3436c7ff7243cd42eda72ad83/schema.sql
5. Enable the extension via the ACP Extension manager (currently located in the System tab of the ACP) or the command-line extension manager (in the `phpbb/phpBB/develop/` directory).

Contributing
=======
I welcome contributions from anyone who is willing to help out. Simply fork this repository, create a new topic branch based on the `develop` branch, make your changes, send a Pull Request (PR) and I will review it. We can discuss and I will eventually merge it in if it looks good. I may enforce some more-strict guidelines on this if I get a lot of contributions so we can keep it organized, but as of now there are no branch name or commit message guidelines. I do ask that you try to follow the phpBB coding Guidelines in general. Remember that the minimum PHP version for this extension is 5.4, so you may take advantage of features introduced in that version (e.g. short array syntax, et al).

Giving Back
=======
Everything I do for phpBB, from MOD/Extension creation to core development, is volunteer work; I do not get any monetary compensation aside from donations. If you appreciate my work and want to buy me a cup of coffee, you are welcome to send me a donation via my PayPal (send it to imkingdavid[at]gmail[dot]com). Of course, all donations are voluntary and any amount is greatly appreciated.

License
=======
As is the case with all of my MODs and Extensions (as well as phpBB itself), this code is licensed under the GPL v2 license. It is provided as is without warranty. You can use it both commercially and personally, and you may modify and redistribute as you wish. The only requirement is that the original copyright remains.
