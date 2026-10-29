<?php
namespace InProsper\Controllers;
use InProsper\Core\Auth;
use InProsper\Repositories\UserRepository;
use InProsper\Repositories\MerchantRepository;
use InProsper\Repositories\NotificationRepository;
use InProsper\Services\NotificationService;
final class AuthController {
    public function __construct(private UserRepository $users, private MerchantRepository $merchants, private NotificationService $notifications) {}
    public function login(): void {
        $email=trim((string)($_POST['email']??'')); $password=(string)($_POST['password']??''); $user=$this->users->findByEmail($email);
        if(!$user || !password_verify($password,$user['password_hash'])) { flash('error','Email atau kata sandi tidak sesuai.'); redirect('?page=login'); }
        if($user['role']==='merchant') { $m=$this->merchants->byUser((int)$user['user_id']); if(!$m || $m['verification_status']!=='disetujui') { flash('error','Akun merchant belum disetujui admin. Status: '.($m['verification_status']??'belum terdaftar').'.'); redirect('?page=login'); } }
        Auth::login($user); flash('success','Selamat datang, '.$user['name'].'!');
        redirect(match($user['role']){'admin'=>'?page=admin','merchant'=>'?page=merchant','default'=>'?page=catalog'});
    }
    public function register(): void {
        $name=trim((string)($_POST['name']??'')); $email=strtolower(trim((string)($_POST['email']??''))); $password=(string)($_POST['password']??''); $role=(string)($_POST['role']??'konsumen'); $phone=trim((string)($_POST['phone']??''));
        if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8 || !in_array($role,['konsumen','merchant'],true)) { flash('error','Lengkapi data dengan benar. Kata sandi minimal 8 karakter.'); redirect('?page=register'); }
        if($this->users->findByEmail($email)) { flash('error','Email sudah terdaftar.'); redirect('?page=register'); }
        try {
            $doc=null; if($role==='merchant') { $doc=\upload_file('document','docs',['application/pdf','image/jpeg','image/png','image/webp']); if(!$doc) throw new \RuntimeException('Dokumen izin usaha/identitas wajib diunggah.'); if(trim((string)($_POST['business_name']??''))==='' || trim((string)($_POST['business_address']??''))==='') throw new \RuntimeException('Nama usaha dan alamat usaha wajib diisi.'); }
            $userId=$this->users->create($name,$email,$password,$role,$phone);
            if($role==='merchant') $this->merchants->create($userId,trim($_POST['business_name']),trim($_POST['business_address']),trim((string)($_POST['business_category']??'Lainnya')),$doc);
            flash('success',$role==='merchant'?'Pendaftaran berhasil. Akun menunggu verifikasi admin.':'Akun berhasil dibuat. Silakan masuk.'); redirect('?page=login');
        } catch(\Throwable $e) { flash('error',$e->getMessage()); redirect('?page=register'); }
    }
    public function logout(): void { Auth::logout(); flash('success','Kamu telah keluar.'); redirect('?page=login'); }
}
