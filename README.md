# Money Manager

Welcome to Money Manager, a personal budget management system designed to streamline your financial tracking process. Whether you're meticulously planning every penny or simply aiming for a clearer picture of your monthly finances, Money Manager is here to simplify the process.

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Technologies Used](#technologies-used)
- [Contributing](#contributing)
- [License](#license)

## Introduction

Money Manager is born from the real-world experience of managing monthly expenses. Inspired by the need for a comprehensive yet user-friendly budgeting tool, this project aims to provide individuals with a clear overview of their financial health.

## Features

- **Expense Tracking**: Easily track your expenses and incomes, ensuring you're always aware of your financial status.
- **Budget Allocation**: Allocate your salary into different categories, whether it's for bills, savings, or investments, ensuring you stay within your means.
- **Monthly Analysis**: Review your spending patterns and financial habits on a monthly basis, empowering you to make informed decisions.
- **Transaction Management**: Mark expenses as 'done' with each transfer or bill payment, keeping your records accurate and up-to-date.

## Installation

To get started with Money Manager, follow these simple steps:

1. Clone the repository: `git clone https://github.com/lucianooliva/money-manager.git`
2. Navigate to the project directory: `cd money-manager`
3. Install dependencies: `composer install && cd vue && npm install && cd ..`
4. Set up your environment variables by copying the `.env.example` file to `.env` and configuring it to match your environment. Don't forget to configure the file in `vue/config/index.js` accordingly.
5. Run migrations: `php artisan migrate`
6. If you have configured a working email service in `.env` file, go to `vue/config/index.js` and change the `emailSendingIsConfigured` boolean constant to `true`
7. Start the backend server: `php artisan serve`
8. In another terminal instance, run the frontend server: `npm run dev`
7. Access the application at `http://<viteServerHost>:<viteServerPort>`

## Usage

Once installed, you can start managing your budget through the intuitive user interface provided by Money Manager. Simply log in, input your income, allocate budgets to different categories, and start tracking your expenses. Mark each transaction as 'done' to keep your records accurate, and review your monthly analysis to gain insights into your spending habits.

## Technologies Used

- **Backend**: Laravel
- **Frontend**: Vue.js 3
- **Database**: MySQL

## Contributing

Contributions are welcome! If you have any ideas, suggestions, or bug fixes, feel free to open an issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE). Feel free to use, modify, and distribute it as per the terms of the license.