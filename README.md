# Travel Management System

A comprehensive web-based system for managing travel itineraries, guide assignments, and group bookings.

## Features

- **Dashboard Overview**
  - Real-time statistics and summaries
  - Upcoming arrivals notifications
  - Monthly distribution charts
  - Financial summaries

- **Itinerary Management**
  - Create and manage travel itineraries
  - Track group details and schedules
  - Monitor payment status
  - Manage completion status

- **Guide Assignment**
  - Assign guides to groups
  - Track guide availability
  - Manage guide profiles

- **Reporting System**
  - Generate financial reports
  - Track monthly distributions
  - Monitor booking trends

## Technologies Used

- PHP 8.2
- MySQL
- Bootstrap 5.3
- jQuery 3.6
- Chart.js
- Bootstrap Icons

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/travel-management-system.git
   ```

2. Import the database:
   - Create a new MySQL database
   - Import `database.sql` file

3. Configure the database connection:
   - Copy `config/database.example.php` to `config/database.php`
   - Update the database credentials in `config/database.php`

4. Set up your web server:
   - Point your web server to the project directory
   - Ensure PHP 8.2 or higher is installed
   - Enable required PHP extensions (mysqli, json)

5. Access the system:
   - Open your browser and navigate to the project URL
   - Default login credentials:
     - Username: admin
     - Password: admin123

## Directory Structure

```
travel-management-system/
├── api/                 # API endpoints
├── assets/             # Static assets (CSS, JS, images)
├── config/             # Configuration files
├── includes/           # PHP includes
├── components/         # Reusable components
└── sql/               # Database scripts
```

## Security Features

- Password hashing
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support


For support, please email alvinchipmunk196@gmail.com or visit [chipmunk-tech](https://github.com/wikiproject1). 
