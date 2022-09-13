1. Clone the Repository from given link
2. Go inside the cloned folder and run "composer install" command
3. Copy .env.example to .env with command - "cp .env.example .env"
4. Create a personal_access_token from github and copy it in env
    -GITHUB_PERSONAL_ACCESS_TOKEN={YOUR_PERSONAL_ACCESS_TOKEN}
5. Run php "artisan key:generate" command to generate app key
6. Run "php artisan serve" command to run the project
7. for Frontend directly hit the url on which the server is started (eg: 127.0.0.1:8000)
8. For Backend Open postman and hit a GET request to {Base_url}/api/most-starred-repos