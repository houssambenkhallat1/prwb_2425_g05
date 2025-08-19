# Djote Forms - Online Form Management Application

## 📝 Project Description

Djote Forms is a web-based form management application inspired by Google Forms and JotForms. It allows users to create, share, and analyze online forms with various question types, providing a complete solution for data collection and analysis.

## 🌟 Key Features

### User Management
- **Authentication system** with role-based access (admin, user, guest)
- **User registration** with password validation
- **User profiles** with editable information management

### Form Management
- **Create/Edit forms** with title, description, and visibility settings
- **Multiple question types**:
  - Short text (short)
  - Long text (long)
  - Date picker (date)
  - Email validation (email)
  - Single choice (radio)
  - Multiple choice (checkbox)
- **Form sharing** with different access levels (user, editor)
- **Color coding** system for form organization

### Form Responses
- **Instance creation** for answering questions
- **Real-time validation** of responses
- **Submission handling** and completed instance management
- **Responsive interface** optimized for mobile/desktop

### Analytics & Reporting
- **Question statistics** with interactive charts
- **Data export** capabilities
- **Trend visualization** and insights

## 🏗️ Technical Architecture

### Technology Stack
- **Backend**: PHP 8.2+ with MVC architecture
- **Database**: MySQL 8.0 with predefined schema
- **Frontend**: HTML5, CSS3, JavaScript (vanilla & jQuery)
- **UI Framework**: Bootstrap 5.3
- **Charts**: Chart.js for data visualization

### Project Structure
```
prwb_2425_xyy/
├── app/
│   ├── controllers/          # Application controllers
│   ├── models/              # Data models and business logic
│   └── views/               # View templates
├── public/
│   ├── assets/
│   │   ├── css/            # Stylesheets
│   │   ├── js/             # JavaScript files
│   │   └── images/         # Image assets
│   └── index.php           # Application entry point
├── database/
│   ├── prwb_2425_xyy.sql          # Database schema
│   └── prwb_2425_xyy_dump.sql     # Sample data
├── config/
│   ├── dev.ini             # Development configuration
│   └── prod.ini            # Production configuration
├── .htaccess               # Apache configuration
└── README.md
```

## 🚀 Installation & Setup

### Prerequisites
- Apache web server with PHP 8.2+
- MySQL 8.0+
- Composer (for dependencies if needed)

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/houssambenkhallat1/prwb_2425_g05.git
   cd djote-forms
   ```

2. **Setup database**:
   ```bash
   mysql -u root -p < database/prwb_2425_xyy.sql
   mysql -u root -p prwb_2425_xyy < database/prwb_2425_xyy_dump.sql
   ```

3. **Configure application**:
   - Edit `config/dev.ini` with your database credentials
   - Ensure proper file permissions for web directories

4. **Access the application**:
   Open http://localhost/djote-forms/ in your browser

### Test Accounts
| Email | Password | Role |
|-------|----------|------|
| admin@epfc.eu | Password1, | admin |
| bepenelle@epfc.eu | Password1, | user |
| guest@epfc.eu | (none) | guest |

## 🧩 System Design

### Database Schema
The database includes the following main tables:
- `users` - User management and authentication
- `forms` - Form definitions and metadata
- `questions` - Question definitions and types
- `instances` - Response sessions
- `answers` - User responses to questions
- `user_form_accesses` - Form sharing permissions
- `form_colors` - Form color associations

### MVC Architecture
The application follows the Model-View-Controller pattern:
- **Models**: Data management and business logic
- **Views**: Display templates (HTML/PHP)
- **Controllers**: Request handling and coordination

## 💡 Advanced Features

### Search & Filtering
- **Full-text search** across forms
- **Color-based filtering** with multiple criteria
- **State propagation** between pages

### Dynamic Interactions
- **Drag & drop** for question reordering
- **Modal dialogs** for confirmations and actions
- **Real-time AJAX validation**
- **Interactive charts** for analytics

### User Experience
- **Consistent navigation** with contextual back buttons
- **Visual feedback** for user actions
- **Responsive design** adapted for all devices

## 🛠️ Development

### Code Standards
- Follows PSR-1/PSR-12 standards
- HTML5/W3C validation compliance
- Well-commented code with documentation

### Best Practices
- SQL injection protection
- Server-side and client-side data validation
- Error handling and logging
- Performance optimization

### Testing
- Manual feature testing
- Cross-browser validation
- Load and performance testing

## 📊 Project Management

### Methodology
- Iterative development with three iterations
- Code review and peer validation
- Technical and user documentation

### Deliverables
- Commented source code
- Database with test datasets
- Technical documentation
- User guide

## 🔒 Security

### Implemented Measures
- **Password hashing** using password_hash()
- **CSRF protection** on forms
- **Strict input validation**
- **Role-based access control**

### Security Best Practices
- Prepared SQL statements (PDO)
- Output escaping (htmlspecialchars)
- Login attempt limitations

## 🌐 Browser Compatibility

### Supported Browsers
- Chrome/Edge 
- Firefox 
- Safari 

### Responsive Design
- Desktop, tablet, and mobile optimized

## 📈 Future Enhancements

### Planned Features
- PDF export of results
- Form templates library
- REST API for integrations
- Approval workflows
- Notifications and reminders



## 📄 License

This project was developed as part of academic coursework at EPFC. All rights reserved.

## 🆘 Support

For questions or issues, please:
- Open an issue on GitHub
- Contact the development team

## 🎯 Getting Started

### Quick Start Guide

1. **First Login**: Use the admin account to explore the system
2. **Create a Form**: Navigate to "Create Form" and add your questions
3. **Share Your Form**: Set permissions and share with users
4. **Collect Responses**: Monitor submissions in real-time
5. **Analyze Results**: View statistics and export data

### Key Concepts

- **Forms**: Container for questions with metadata
- **Questions**: Individual form fields with specific types
- **Instances**: User response sessions
- **Sharing**: Permission-based form access control

## 🔧 Configuration

### Environment Setup
Edit the configuration files in the `config/` directory:

**Development** (`config/dev.ini`):
```ini
[database]
host = localhost
dbname = prwb_2425_xyy
username = your_username
password = your_password
```

**Production** (`config/prod.ini`):
```ini
[database]
host = your_production_host
dbname = prwb_2425_xyy
username = your_prod_username
password = your_prod_password
```


---

**Built with ❤️ by the EPFC Development Team**
