# webetdesign/wd-maintenance-bundle

Bundle to temparary disable website. Work for multisite. 

## Requirement
- PHP ^8.0
- symfony ^5

## Installation
Add the repo to your composer.json

```json
"repositories": [
	 {
	   "type": "git",
	   "url": "https://github.com/webetdesign/wd-maintenance-bundle.git"
	 }
],
```

 And 

```
composer require webetdesign/wd-maintenance-bundle
```

Register the bundles in `config/bundles.php`

``` php 
return [
    ...
    WebEtDesign\MaintenanceBundle\WDMaintenanceBundle::class => ['all' => true],
    ...
];
```

Register routes

```yaml 
# config/routes/wd_maintenance.yaml
wd_maintenance:
  resource: "@WDMaintenanceBundle/Resources/config/routes.yaml"
```

Exclude maintenance file
```gitignore
#.gitignore
# Used to store IP
/var/.maintenance
```
## Enable maintenance mode
If a client with a IP in the list try to access website during maintenance mode, he won't be blocked
````shell
# Provide a list of white IP
bin/console app:maintenance-mode --on "ip_1,ip_2,ip_3"

# Specify the CmsSite host to enable (default all website enabled)
bin/console app:maintenance-mode --on "ip_1,ip_2,ip_3" mysite.com
````

## Disable maintenance mode
````shell
# Disable for all website
bin/console app:maintenance-mode --off

# Specify the CmsSite host to disable (default all website edited)
bin/console app:maintenance-mode --off mysite.com
````

## White link
Configure white list hash
```dotenv
MAINTENANCE_BUNDLE_HASH=my_hash
```
You can send a link to somenone to give him access whitout adding is IP. Provide a value
to MAINTENANCE_BUNDLE_HASH in the .env.local file.
````shell
bin/console app:maintenance-mode-link
````
Don't forget to ask IKOULA to trust cookie used to verify the link : MAINTENANCE_WHITE_LINK