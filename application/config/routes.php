<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['admin'] = 'admins/index';
$route['admin/dashboard'] = 'admins/dashboard';
$route['admin/admission'] = 'admins/admission';
$route['admin/casefile/(:any)'] = 'admins/casefile/$1';
$route['admin/login'] = 'admins/login';
$route['admin/logout'] = 'admins/logout';
$route['admin/student'] = 'admins/student';
$route['admin/staff'] = 'admins/staff';
$route['admin/course'] = 'admins/course';
$route['admin/module'] = 'admins/module';


$route['admin/uploadCSV'] = 'admins/uploadCSV';

//route for assignment
$route['assignment/index'] = 'assignments/index';
$route['assignment/add'] = 'assignments/add';
$route['assignment/edit'] = 'assignments/edit';
$route['assignment/view'] = 'assignments/view';
$route['assignment/upload'] = 'assignments/upload';



//route for the pages
$route['(:any)'] = 'pages/view/$1';


//student pages
$route['student/dashboard'] = 'students/dashboard';
$route['student/login'] = 'students/login';
$route['student/logout'] = 'students/logout';

//leader pages
$route['leader/dashboard'] = 'leaders/dashboard';

//tutor pages
$route['tutor/dashboard'] = 'tutors/dashboard';

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
