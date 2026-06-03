# BPO4All - BPO Management System

BPO4All is a comprehensive management and attendance tracking system tailored for Business Process Outsourcing (BPO) companies and call centers. The system is designed to streamline campaign organization, manage employee records, and track daily attendances with specialized metrics such as call times and daily salaries.

## Features

- **Role-Based Access Control:**
  - **Super Admin:** Full system access.
  - **HR Manager:** Manages employees, users, and overall company size metrics.
  - **Team Leader:** Manages specific campaigns assigned to them and tracks the attendance of their agents.
  - **CEO:** High-level dashboard view to oversee total workforce, campaigns, agents, and overall role distributions.

- **Campaign Management:**
  Create, update, and organize campaigns. Assign specific team leaders and agents to different campaigns to ensure organized workflows.

- **Employee (Agent) Management:**
  Maintain detailed records of call center agents, including their active/inactive/terminated statuses and campaign assignments.

- **Advanced Attendance Tracking:**
  Record daily attendance across campaigns. Depending on the campaign configuration, the system can track basic attendance (Present, Absent, Late, Leave) or advanced call center metrics such as `Call Time` and `Daily Salary`.

- **Dynamic Dashboards:**
  Each user role has a personalized dashboard presenting the most relevant data and metrics to their responsibilities.

## Technologies Used

This project is built on a modern, robust tech stack to ensure performance, maintainability, and scalability:

- **Backend:** [Laravel](https://laravel.com/) (PHP Framework)
- **Frontend / Templating:** [Blade Templating Engine](https://laravel.com/docs/blade)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/) for a utility-first, responsive, and modern UI design.
- **Database:** Relational Database management (MySQL / SQLite / PostgreSQL) via Laravel's Eloquent ORM.
- **Authentication:** Laravel's built-in authentication scaffolding.

## Installation & Setup

1. Clone the repository.
2. Run `composer install` to install PHP dependencies.
3. Run `npm install` and `npm run build` to compile the frontend assets (Tailwind CSS).
4. Copy `.env.example` to `.env` and configure your database credentials.
5. Generate an app encryption key using `php artisan key:generate`.
6. Run database migrations and seeders using `php artisan migrate --seed` (This will create the default Super Admin, HR Manager, Team Leader, and CEO accounts).
7. Start the development server with `php artisan serve`.

## License

This project is proprietary and intended for internal use by the organization.
