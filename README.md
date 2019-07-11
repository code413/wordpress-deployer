# Wordpress Deployer
Deploy a Wordpress website from staging to live and revert. 

## Installation

Clone the repository from Github

    $ git clone git@github.com:code413/wordpress-deployer.git
    
Switch to the repo folder

    cd wordpress-deployer
    
Install all the dependencies

    composer install
    npm install
    
Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env
    
Generate a new application key

    php artisan key:generate
    
Add the User email and password to login 
     SUPER_USER=
     SUPER_PASSWORD=
    
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
1. Create a profile with all the credentials. 
2. Go to profiles and create replacements.
3. Fill up the file path and file pattern if replacement type is file.
4. Create version
5. Deploy your new version.

### Create a profile
1. Database user should have all the access to create or delete database, root user of the database,
2. Database password should be sudo database password. 
3. Database storage path, where you want to store the database file.
4. Project root path should be the path of the project, which you want to copy.
5. Deployment path is a path where you will keep the copied file from the root project path.
6. Symlink path is the path of the website folder or the website path.

### Create replacements
1. Create a database replacement to create new versions. 
2. Replacements from is the text you want replace.
3. Replacements to is the text will be replaced.
4. For the file replacement, use '/' to replace in root folder, or '/folder-name' to replace in folder,
5. Use the pattern '*.php' to change the php file, use the file extensions to change the files.

*Minimum 1 database replacements is required. 

*Multiple replacements is allowed.


## Workflow
1. Create a profile.
2. Create one/more replacements. 
3. Create new version.
4. Deploy the version. 



## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.


## License
[MIT](https://choosealicense.com/licenses/mit/)
