# Zero Downtime Wordpress Deployment
Create and manage multiple environments for your Wordpress wesbite. Deploy from staging to production with zero downtime.

## Installation

Clone this repository from Github.

    $ git clone git@github.com:code413/wordpress-deployer.git
    
Switch to the repo directory.

    cd wordpress-deployer
    
Install all the dependencies.

    composer install
    npm install
    
Copy the example env file and make the required configuration changes in the .env file.

    cp .env.example .env
    
Generate a new application key.

    php artisan key:generate
    
Define a Super User in your env file.
```
SUPER_USER=
SUPER_PASSWORD=
```

Run the database migrations (Set the database connection in .env before migrating)

    php artisan migrate
    
In to make zero downtime put the code in ngnix config
        
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        
It should look like this, 
    
    location ~ \.php$ {
        try_files     $uri =404;

        fastcgi_pass         unix:/var/run/php5-fpm.sock;

        include  fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
    
TL;DR command list

    git clone git@github.com:code413/wordpress-deployer.git
    cd wordpress-deployer
    composer install
    npm install
    cp .env.example .env
    php artisan key:generate

Make sure you set the correct database connection information before running the migrations

    php artisan migrate
   

## Usage

### 1. Create a profile
1. Database root user, `root-user`, Database host ip, `128.177.111.122` or local host ip `127.0.0.1`
2. Sudo database password `database-password`. 
3. Website path, `/home/root-user/your-website.com/`.
4. Symlink path for new website `/home/root-user/new-website.com/`.


### 2. Create replacements
1. Replace from `website.com` to, replace to `new-website.com` in database or file.
2. Replace `*.php` php files in, `/wp-content` or `/wp-content/folder-name` directory,

*Minimum 1 database replacements is required.


## Workflow
1. Create a profile.
2. Create one/more replacements. 
3. Create new version.
4. Deploy the version. 


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.


## License
[MIT](https://choosealicense.com/licenses/mit/)
