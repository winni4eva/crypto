## Vue / Laravel - Blockhain Wallet Management App 
A simple application that helps you manage your crypto currency wallets using Bitgo

## Setup
1. Clone repository
2. Change directory to project root
3. Copy .env.example file and rename to .env
4. Update database credentials in .env file
5. Create Bitgo test account https://test.bitgo.com
6. Create an access token from user settings menu
7. Create Twilio account https://www.twilio.com
8. Update these .env keys
    ```
        TWILIO_ACC_ID=xxxx-xxxxx-xxxxx
        TWILIO_TOKEN=xxxx-xxxxx-xxxxx
        TWILIO_NUMBER=xxxx-xxxx-xxxxx

        BITGO_EXPRESS_ENDPOINT=http://localhost:3080
        BITGO_TOKEN=xxxx-xxxx-xxxx
    ```
9. Update mail config. You can create a mailtrap account for testing
    ```
        MAIL_DRIVER=smtp
        MAIL_HOST=smtp.mailtrap.io
        MAIL_PORT=587
        MAIL_USERNAME=
        MAIL_PASSWORD=
        MAIL_ENCRYPTION=tls
    ```
10. Run command: composer install
11. Run command: npm install
12. Run command: php artisan key:generate
13. Run command: php artisan jwt:secret
14. Run command: php artisan migrate
15. Run command: php artisan db:seed
16. Setup Bitgo express server. There are two options here
    - https://github.com/BitGo/BitGoJS
    - docker pull bitgo/express 
17. BitGoJS has a guide on how to setup
18. Docker Setup
    ```
        Make sure you have docker setup on your environment
        Run command: docker pull bitgo/express 
        Next run command: docker images 
        Run image using Repository name or Image Id
        Example: docker run -p 3080:3080 bitgo/express
    ```

FIX---
Create Wallet something unusual happened error response
Remove wallet user filter scope query comment
