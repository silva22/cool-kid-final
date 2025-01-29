# THE PROBLEM TO BE SOLVED

The objective was to create a dynamic role-sensitive user management system using WordPress. This included displaying different elements, pages or items and user information based on roles, while ensuring unauthorized users are restricted from viewing or interacting with sensitive elements.

# TECHNICAL SPECIFICATION

Given the objectives associated with the project, different tools and technical elements were used to find an optimal solution for the problem at hand. For instance, hooks like wp_nav_menu_objects were leveraged to dynamically control visibility of menus, custom shortcodes were used to display user information, and the use of conditional statements in the template files to restrict access to sensitive elements. A custom API plugin was developed to aid management of user roles, while others such as WPForms were used to develop the necessary forms for user interaction.

# TECHNICAL DECISIONS

## 1. Custom API Plugin Development
A custom API plugin was developed to aid in the management of user roles. This plugin was critical in bypassing issues created by an attempt to utilize the default REST API to engage allow setting of user roles.

## 2. Using WordPress Hooks
Hooks like `wp_nav_menu_objects` were leveraged to dynamically control visibility of menus by deploying the necessary filters to hide and display pages, thereby improving user experience and security. At the same time, the use of WPForms was critical in creating login and registration pages.

## 3. Plugins
Currently active WordPress plugins such as WPForms, Members, Code Snippet, and EASY WP SMTP were selected due to the need to enable user registration and login, user role management, addition of custom functions, and communication with users, especially where account activation and password resets are concerned.

## dynamic shortcodes
Additional functions and other plugin-based shortcodes provided the much-needed flexibility in rendering user information, minimizing redundancies and enhancing the scalability of the system.

# HOW IT ACHIEVES THE DESIRED ADMIN OUTCOMES

The adopted solutions focused on ensuring that all information in the system is compartmented. As a result, it fulfills the set assessment requirements by providing a means for users to onboard the system and managers to manage accessibility of each user. In the process, it ensures that all users have access to appropriate data, aligning with their specific story for secure, role-based management.

# HOW I APPROACH A PROBLEM

To solve the problem, I first focused on breaking it down into manageable components - in this case, this resulted in dealing with user login and registration elements of the problem, before focusing on each user story and the underlying requirements. Through this dynamic breakdown, it is easy to conduct detailed research and identify dependencies and constraints of the project. In the process, I am able to prioritize flexibility and scalability, while adhering to the best practices.

# WHY I CHOSE THIS DIRECTION
I chose this direction because it provides a clear way to identify key WordPress plugins and native tools that can be useful in the entirety of the project. It also minimizes complexities by ensuring compatibility of the system with different components with emphasis on maintainability and performance.

# WHY IS THIS DIRECTION BETTER

The adopted direction focuses on combining native functionality with minimal plugins to create a light widget solution that is secure and easy to extend. As such, I was able to meet the set requirements efficiently without introducing unnecessary overheads or complexities.
