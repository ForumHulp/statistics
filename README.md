Referrer
===========

Referrer displays an overview of referrer logs in Maintenance module of ACP. This extension logs all referrers it finds and keep track of hits, first visit and last visit. A cron job can prune the referrers after a configurable time.

## Requirements
* phpBB 3.1.0-dev or higher
* PHP 5.3.3 or higher

## Installation
You can install this extension on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by doing the following:

1. Copy the [entire contents of this repo](https://github.com/ForumHulp/referrer/archive/master.zip) to `FORUM_DIRECTORY/ext/forumhulp/referrer/`.
2. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions`.
3. Click Referrer => `Enable`.

Note: This extension is in development. Installation is only recommended for testing purposes and is not supported on live boards. This extension will be officially released following phpBB 3.1.0. Extension depends on two core changes.

## Usage
### Referrer page
To check the referrers navigate in the ACP to `Maintenance -> Referrer Log`.

### Referrer prune settings
You can utilize phpBB cron for pruning the log files. Cron will look once a day and prune referrer logs older the "day" set in ACP `General -> Board configuration -> Board features -> Referrer days`. Set days to zero wil disable this cron job.

## Update
1. Download the [latest ZIP-archive of `master` branch of this repository](https://github.com/ForumHulp/referrer/archive/master.zip).
2. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Referrer => `Disable`.
3. Copy the contents of `referrer-master` folder to `FORUM_DIRECTORY/ext/forumhulp/referrer/`.
4. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Referrer => `Enable`.
5. Click `Details` or `Re-Check all versions` link to follow updates.
6. Or use our Upload Extensions extension

## Uninstallation
Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Referrer => `Disable`.

To permanently uninstall, click `Delete Data` and then you can safely delete the `/ext/forumhulp/referrer/` folder.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2014 - John Peskens (ForumHulp.com)