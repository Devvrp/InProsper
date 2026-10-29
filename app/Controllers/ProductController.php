<?php
namespace InProsper\Controllers;
use InProsper\Core\Auth;
use InProsper\Repositories\ProductRepository;
use InProsper\Repositories\MerchantRepository;
final class ProductController {
    public function __construct(private ProductRepository $products, private MerchantRepository $merchants) {}
    public function catalog(): void {
        $filters=['q'=>trim((string)($_GET['q']??'')),'category'=>trim((string)($_GET['category']??'')),'max_price'=>trim((string)($_GET['max_price']??'')),'sort'=>$_GET['sort']??'expiry'];
        render('catalog/index',['title'=>'Selamatkan makanan, hemat lebih banyak','products'=>$this->products->search($filters),'filters'=>$filters]);
    }
    public function detail(): void { $p=$this->products->findPublic((int)($_GET['id']??0)); if(!$p){http_response_code(404);flash('error','Produk tidak ditemukan atau tidak tersedia.');redirect('?page=catalog');} $p['available_stock']=$this->products->availableStock((int)$p['product_id']); render('catalog/detail',['title'=>$p['name'],'product'=>$p]); }
    public function form(): void {
        Auth::requireRole('merchant'); $m=$this->merchants->byUser((int)Auth::user()['user_id']); if(!$m || $m['verification_status']!=='disetujui'){flash('error','Hanya merchant terverifikasi yang dapat mengelola produk.');redirect('?page=merchant');}
        $p=null; if(!empty($_GET['id'])) $p=$this->products->findForMerchant((int)$_GET['id'],(int)$m['merchant_id']);
        render('merchant/product_form',['title'=>$p?'Ubah produk':'Tambah produk surplus','merchant'=>$m,'product'=>$p]);
    }
    public function save(): void {
        Auth::requireRole('merchant'); $m=$this->merchants->byUser((int)Auth::user()['user_id']); if(!$m || $m['verification_status']!=='disetujui') throw new \RuntimeException('Akun merchant belum terverifikasi.');
        $d=['name'=>trim((string)($_POST['name']??'')),'description'=>trim((string)($_POST['description']??'')),'category'=>trim((string)($_POST['category']??'Lainnya')),'normal_price'=>(float)($_POST['normal_price']??0),'discount_percent'=>(float)($_POST['discount_percent']??0),'stock'=>(int)($_POST['stock']??0),'expiry_date'=>(string)($_POST['expiry_date']??''),'expiry_time'=>(string)($_POST['expiry_time']??''),'photo_url'=>null,'is_active'=>isset($_POST['is_active'])];
        if($d['name']==='' || $d['normal_price']<=0 || $d['discount_percent']<30 || $d['discount_percent']>70 || $d['stock']<0 || !$d['expiry_date'] || strtotime($d['expiry_date'].' '.($d['expiry_time']?:'23:59'))<=time()) throw new \RuntimeException('Periksa isian: diskon wajib 30–70%, harga harus positif, stok tidak negatif, dan batas layak konsumsi harus di masa depan.');
        $d['photo_url']=\upload_file('photo','products',['image/jpeg','image/png','image/webp']);
        if(!empty($_POST['product_id'])) { $id=(int)$_POST['product_id']; if(!$this->products->findForMerchant($id,(int)$m['merchant_id'])) throw new \RuntimeException('Produk tidak ditemukan.'); $this->products->update($id,(int)$m['merchant_id'],$d); flash('success','Produk berhasil diperbarui.'); }
        else { $this->products->create((int)$m['merchant_id'],$d); flash('success','Produk berhasil dipublikasikan.'); }
        redirect('?page=merchant');
    }
    public function deactivate(): void { Auth::requireRole('merchant'); $m=$this->merchants->byUser((int)Auth::user()['user_id']); if(!$m)throw new \RuntimeException('Profil merchant tidak ditemukan.'); $this->products->deactivate((int)($_POST['product_id']??0),(int)$m['merchant_id']); flash('success','Listing dinonaktifkan.'); redirect('?page=merchant'); }
}
