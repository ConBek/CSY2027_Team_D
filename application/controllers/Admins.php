<?php

class Admins extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        // $this->load->model('Admin');

         
        // This section works but commented for now
        
        if($this->router->fetch_method() != 'login' && $this->session->userdata('type') != 1){
            header('Location: login');
        } 
        
        
    }


    public function loadViews($page, $title, $data = [])
    {
        $this->load->view('layouts/header', ['title' => $title]);
        if($this->session->userdata('type') == 1){
            $this->load->view('layouts/adminNav');
        }else{
            $this->load->view('layouts/siteNav');
        }
        $this->load->view('admin/' . $page, $data);
        if($page == 'login'){
            $this->load->view('layouts/footer');
        }else{
            $this->load->view('layouts/adminfooter');
        }
    }

    public function index()
    {
        
    }

    public function dashboard()
    {
        $this->loadViews('dashboard', 'Dashboard');
    }

    public function admission()
    {
        $data['admissions'] = $this->admin->tableGenerator($this->admin->getAdmissions());
        $this->loadViews('admission', 'Admission', $data);
    }

    public function login()
    {
        if($this->session->userdata('type') == 1){
            header('Location:dashboard');
        }

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->loadViews('login', 'Staff LogIn');

        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $staff_id = $this->admin->login($email, $password);

            if ($staff_id) {
                $staff_data = array(
                    'id' => $staff_id['staff_id'],
                    'name' => $staff_id['firstname'],
                    'middlename' => $staff_id['middlename'],
                    'surname' => $staff_id['surname'],
                    'address' => $staff_id['address'],
                    'subject' => $staff_id['subject'],
                    'contact' => $staff_id['contact'],
                    'email' => $email,
                    'type' => $staff_id['role']
                );
                $this->session->set_userdata($staff_data);
                switch ($staff_data['type']) {
                    case '1':
                        redirect('admin/dashboard');
                        break;
                    case '2':
                        redirect('leader/dashboard');
                        break;
                    case '3':
                        redirect('tutor/dashboard');
                        break;
                }
            } else {
                redirect('admin/login');
            }
        }
    }
    public function logout()
    {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('type');

        redirect('/');
    }

    public function student()
    {
        $data['students'] = $this->admin->tableGenerator($this->admin->getStudents());
        $this->loadViews('student', 'Students', $data);
    }
    public function add()
    {
        $this->form_validation->set_rules('firstname', 'Firstname', 'trim|required');
        $this->form_validation->set_rules('middlename', 'Middlename', 'trim|required');
        $this->form_validation->set_rules('surname', 'Surname', 'trim|required');
        $this->form_validation->set_rules('tempAddress', 'Temporary Address', 'trim|required');
        $this->form_validation->set_rules('permAddress', 'Permanent Address', 'trim|required');
        $this->form_validation->set_rules('contact', 'Contact', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('qualification', 'Qualifications', 'trim|required');
        $this->form_validation->set_rules('courseCode', 'Course Code', 'trim|required');
        
        if ($this->form_validation->run() === FALSE) {
            $data['courses'] = $this->admin->add();
            $this->loadViews('add', 'Add Student', $data);

        }else{
            $add = $this->admin->addStudent();
            if($add){
                redirect('admin/admission');
            }else{
                redirect('admin/add');
            }
        }
    }

    public function uploadCSV()
    {
        if ($this->input->post('upload')) {
            $csvFileName = explode(".", $_FILES['UCASDetail']['name']);
            if (end($csvFileName) == "csv") {
                $this->admin->csvUpload($_FILES['UCASDetail']);
            }
        }
        redirect('admin/admission');
    }

    public function casefile($id)
    {
        $data = $this->admin->getStudentData($id);

        $this->loadViews('casefile', 'Case File',$data);
    }

    public function uploadDoc()
    {
        if($this->input->post('studentDoc')){
            $this->admission->docUpload($_FILES['studentDoc']);
        }
    }
    public function staff($id = false)
    {
        if($id){
            if (isset($_POST['archive'])) {
                $data = ['archive' => '1'];
                $this->admin->assign_archive_staff($id , $data);
                redirect('admin/staff');
            }elseif(isset($_POST['assign'])){
                $data = ['subject' => $this->input->post('subject')];
                $this->admin->assign_archive_staff($id , $data);
                redirect('admin/staff');            
            }
        }
        $data['staff'] = $this->admin->getTable('archive', '0', 'staff');
        $this->loadViews('staff', 'Staff', $data);
    }
    public function staffDetail($id = false)
    {
        $data = $this->admin->getTableData($id, 'staff_id', 'staff');

        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('surname', 'Surname', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('contact', 'Contact', 'required');
        if ($id && isset($_POST['add'])) {
            if ($data['staff_id'] != $_POST['staff_id']) {
                $this->form_validation->set_rules('staff_id', 'Staff id', 'required|integer|is_unique[staff.staff_id]');
            }            
            if ($data['email'] == $_POST['email']) {
                $this->form_validation->set_rules('email', 'Email', 'required');
            }
            else {
                $this->form_validation->set_rules('email', 'Email', 'required|is_unique[staff.email]');
            }
            if ($data['subject'] != $_POST['subject']) {
                $this->form_validation->set_rules('subject', 'Subject', 'is_unique[staff.subject]');
            }
        }else{
            $this->form_validation->set_rules('staff_id', 'Staff id', 'required|integer|is_unique[staff.staff_id]');
            $this->form_validation->set_rules('email', 'Email', 'required|is_unique[staff.email]');
            $this->form_validation->set_rules('password', 'Password', 'required');        
        }
        if ($this->form_validation->run() === FALSE) {
            if ($id) {
                $this->loadViews('staffDetail', 'Edit Staff', $data);    
            } else {
                $data = ['staff_id' => '', 'status' => '3' , 'firstname' => '', 'middlename' => '', 'surname' => '', 
                'address' => '', 'contact' => '', 'email' => '','password' => '', 'subject' => '', 'role' => ''];
                $this->loadViews('staffDetail', 'Add Staff', $data);
            }
        }elseif($id) {
            $this->admin->updateStaff($id);
            redirect('admin/staff');
        }
        else{
            $this->admin->addStaff();
            redirect('admin/staff');
        }
    }

    public function course($id = false)
    {
        if($id){
            if (isset($_POST['archive'])) {
                $data = ['archive' => '1'];
                $this->admin->archiveCourse($id , $data);
                redirect('admin/course');
            }elseif(isset($_POST['delete'])){
                $this->admin->deleteCourse($id);
                redirect('admin/course');            
            }
        }
        $data['courses'] = $this->admin->getTable('archive', '0', 'courses');
        $this->loadViews('course', 'Course' , $data);
    }
    public function courseDetail($id = false)
    {
        $course = $this->admin->getTableData($id, 'course_code', 'courses');

        $this->form_validation->set_rules('course_name', 'Course Name', 'required');
        $this->form_validation->set_rules('course_duration', 'Course Duration', 'required');
        $this->form_validation->set_rules('department_id', 'Department', 'required');

        if ($id && isset($_POST['add'])) {
            if ($course['course_code'] != $_POST['course_code']) {
                $this->form_validation->set_rules('course_code', 'Course Code', 'required|integer|is_unique[courses.course_code]');
            }      
        }          
        else{
            $this->form_validation->set_rules('course_code', 'Course Code', 'required|integer|is_unique[courses.course_code]');
        }
        if ($this->form_validation->run() === FALSE) {
            $department = $this->admin->getTable('','', 'departments');
            $courseLeader = $this->admin->getTable('role', '2' , 'staff');
            if ($id) {
                $data = [
                    'course' => $course,
                    'department' => $department,
                    'courseLeader' => $courseLeader
                ];
                $this->loadViews('courseDetail', 'Edit Course', $data);    
            } else {
                $courseNull = ['course_code' => '', 'course_name' => '' , 'course_duration' => '', 'department_id' => '', 'course_leader' => ''];
                $data = [
                    'course' => $courseNull,
                    'department' => $department,
                    'courseLeader' => $courseLeader
                ];
                $this->loadViews('courseDetail', 'Add Course ', $data);    
            }
        }
        elseif($id){
            $this->admin->updateCourse($id);
            redirect('admin/course');
        }
        else{
            $this->admin->addCourse();
            redirect('admin/course');
        }
    }
    public function module($id = false)
    {
        if($id){
            if (isset($_POST['archive'])) {
                $data = ['archive' => '1'];
                $this->admin->archiveModule($id , $data);
                redirect('admin/module');
            }elseif(isset($_POST['delete'])){
                $this->admin->deleteModule($id);
                redirect('admin/module');            
            }
        }
        $data['modules'] = $this->admin->getTable('archive', '0', 'modules');
        $this->loadViews('module', 'Module' , $data);
    }
    public function moduleDetail($id = false)
    {
        $module = $this->admin->getTableData($id, 'module_code', 'modules');

        $this->form_validation->set_rules('module_name', 'Module Name', 'required');
        $this->form_validation->set_rules('module_duration', 'Module Duration', 'required');
        $this->form_validation->set_rules('course_code', 'Course', 'required');
        if ($id && isset($_POST['add'])) {
            if ($module['module_code'] != $_POST['module_code']) {
                $this->form_validation->set_rules('module_code', 'Module Code', 'required|integer|is_unique[modules.module_code]');
            }      
        }          
        else{
            $this->form_validation->set_rules('module_code', 'Module Code', 'required|integer|is_unique[modules.module_code]');
        }
        if ($this->form_validation->run() === FALSE) {
            $course  = $this->admin->getTable('','','courses');

            if ($id) {
                $data = [
                    'module' => $module,
                    'course' => $course
                ];
                $this->loadViews('moduleDetail', 'Edit Module', $data);    
            } else {
                $moduleNull = ['module_code' => '', 'module_name' => '' , 'module_duration' => '', 'module_leader' => '', 'course_code' => ''];
                $data =[
                    'module' => $moduleNull,
                    'course' => $course
                ];
                $this->loadViews('moduleDetail', 'Add Module ', $data);    
            }
        }
        elseif($id){
            $this->admin->updateModule($id);
            redirect('admin/module');
        }
        else{
            $this->admin->addModule();
            redirect('admin/module');
        }
    }

}
