<?php

namespace App\Controllers;
use App\Models\Product;
use App\Models\Registration;


class Home extends BaseController
{
    public function index()
    {
        // $pdata=new Registration();
        // $data['product']=$pdata->findall();
        // return view('welcome_message',$data);
        return view('login_page');
    }
    
    public function login()
    {
       
        $pdata=new Registration();
        $data=[
            'email'=>$this->request->getPost('email'),
            'password'=>$this->request->getPost('password'),
            
        ];
        $email=$data['email'];
        $pass=$data['password'];
        // $ndata=$pdata->find(`$email`);
        $ndata = $pdata->select('*')
                     ->where('email', $email)
                     ->get()->getResult();
        // print_r($ndata[0]->email);
        
        if((isset($ndata[0]->status)) && ($ndata[0]->password == $pass)){
            if($ndata[0]->status == 0){
                
            return redirect()->to(site_url('/'))->with('message','please change the password and activate the login');
            }else{
                
            $ndata=$ndata = $pdata->select('*')
            ->where('email', $email)
            ->get()->getRowArray();
            
             return view('landingpage',$ndata);
            }
            
        }else{
            return redirect()->to(site_url('/'))->with('message','Invalid Login');
        }
        
       
    }
   
    public function registration()
    {
        $pdata=new Registration();
        $rpassword="ChangePwd".rand(10,100);
        $data=[
            'name'=>$this->request->getPost('name'),
            'dob'=>$this->request->getPost('dob'),
            'email'=>$this->request->getPost('email'),
            'mobile'=>$this->request->getPost('mobile'),
            'address'=>$this->request->getPost('address'),
            'pincode'=>$this->request->getPost('pincode'),
            'password'=>$rpassword,
            'status'=>"0",
        ];
        // print_r($data);
        // die;
        $sentto=$data['email'];

        // $ndata=$pdata->find(`$sentto`);
        $ndata = $pdata->select('*')
        ->where('email', $sentto)
        ->get()->getRowArray();
        if((isset ($ndata['email'])) && ($ndata['email'] == $sentto)){
            return redirect()->to(site_url('useradd'))->with('message','Email already Exist');

        }
        else{
            $email = \Config\Services::email();

            $email->setFrom('fe08a57bab86bd', 'Shubham Test');
            $email->setTo($sentto);
            // $email->setCC('another@another-example.com');
            // $email->setBCC('them@their-example.com');
            
            $email->setSubject('Email Test');
            $email->setMessage('your temporary login credentials as follow'."<br>"."email => $sentto"."  "."pass=>$rpassword"."<br>"."change password using forget password");
            
            if($email->send()){
                $pdata->save($data);
                return redirect()->to(site_url('/'))->with('message','User Registerd Successfully');
            }else{
                echo "notsuccess";
            }
        }
       
        // return view("registration_form");
    }
    public function registrationpage()
    {
        return view('registration_form');
        // echo "shu";
    }
    public function password_page()
    {
        return view('password_change');
    }
    public function changepwd()
    {
        $pdata=new Registration();
       
        $email=$this->request->getPost('email');
        $pass=$this->request->getPost('oldpassword');
        // $ndata=$pdata->find(`$email`);
        $ndata = $pdata->select('*')
                     ->where('email', $email)
                     ->get()->getRowArray();
        
        $id=$ndata['id'];
        if((isset($ndata['email'])) && ($ndata['password'] == $pass)){
            if($ndata['status'] == 0){
                $data=[
                    'email'=>$email,
                    'status'=>1,
                    'password'=>$this->request->getPost('newpassword'),
                    
                ];
                
                $sentemail = \Config\Services::email();

                $sentemail->setFrom('fe08a57bab86bd', 'Shubham Test');
                $sentemail->setTo($email);
                // $email->setCC('another@another-example.com');
                // $email->setBCC('them@their-example.com');
                
                $sentemail->setSubject('Email Test');
                $sentemail->setMessage("Your account is now active");
                $sentemail->send();
                $pdata->update($id,$data);
            return redirect()->to(site_url('/'))->with('message','password change successfully');
            }else{
                $data=[
                    'email'=>$email,
                    
                    'password'=>$this->request->getPost('newpassword'),
                    
                ];
                $pdata->update($id,$data);
                return redirect()->to(site_url('/'))->with('message','password change successfully');
            }
            
        }else{
            return redirect()->to(site_url('passwordpage'))->with('message','Invalid Credentials');
        }
        
    }
    public function otp_page()
    {
        return view('otplogin');
    }
    public function sent_otp()
    {
        $email=$this->request->getPost('email');
        $pdata=new Registration();
        // $ndata=$pdata->find(`$email`);
        $ndata = $pdata->select('*')
                     ->where('email', $email)
                     ->get()->getRowArray();
        $getemail=$ndata['email'];
        $getstatus=$ndata['status'];
        $id=$ndata['id'];
        if(($getemail == $email) && ($getstatus == 1)){
            
            $fourdigitrandom = rand(1000,9999);
            $sentemail = \Config\Services::email();

            $sentemail->setFrom('fe08a57bab86bd', 'Shubham Test');
            $sentemail->setTo($email);
            // $email->setCC('another@another-example.com');
            // $email->setBCC('them@their-example.com');
            
            $sentemail->setSubject('Email Test');
            $sentemail->setMessage("Otp: $fourdigitrandom");
            $sentemail->send();
            // return view('landingpage',$ndata);
            $saveotp=[
                'otp'=>$fourdigitrandom,
            ];
            $pdata->update($id,$saveotp);
            $vdata=[
                'email'=>$getemail,
                'message'=>"Otp sent to your email",

            ];
            // print_r($vdata);
            // die;
            return view('verify',$vdata);

            // return redirect()->to(site_url('verify',$getemail))->with('message',' Otp sent to your email');

        }elseif(($getemail == $phone) && ($getstatus == 0)){
            return redirect()->to(site_url('/'))->with('message','please change password using forget password link');
        }
        else{
            return redirect()->to(site_url('otppage'))->with('message',' phone not Exist');
        }

    }
    public function verify_otp()
    {
        $pdata=new Registration();
       
        $email=$this->request->getPost('email');
        // print_r($email);
        // die;
        $otp=$this->request->getPost('otp');
        // $ndata=$pdata->find(`$email`);
        $ndata = $pdata->select('*')
                     ->where('email', $email)
                     ->get()->getRowArray();
        $getemail=$ndata['email'];
        // print_r($otp);
        // die;
        if((isset($getemail)) && ($ndata['otp'] == $otp)){
            
            $ndata['pdata']=$pdata->select('*')
            ->where('email', $email)
            ->get()->getRowArray();
            
            return view('landingpage',$ndata);
            
        }else{
            $vdata=[
                'email'=>$email,
                'message'=>"Invalid OTP",

            ];
            return view('verify',$vdata);
            // return redirect()->to(site_url('verify'))->with('message','Invalid otp');
        }
    }
}
