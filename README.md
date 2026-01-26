# My Diary Web App

A simple personal diary web application built with PHP.  
Users can register, log in, create diary entries, and manage their profiles. Admins can manage site settings via a Tailwind CSS admin panel.

---

## Features

- User authentication (login & signup)
- Profile management with profile pictures
- Diary entries (create, edit, delete)
- Admin panel for managing users and maintenance mode
- Responsive design: Tailwind for admin, custom CSS for other pages

---

## Frontend / Styling

- **Admin Panel:** Tailwind CSS (via CDN in `admin.php`)  
- **All other pages:** Custom `style.css`  
- Example of including Tailwind in `admin.php`:

```html
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
