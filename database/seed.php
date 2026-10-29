<?php
// Jalankan sekali setelah database/schema.sql diimpor: php database/seed.php
$configFile=__DIR__.'/../config/database.php'; $c=require $configFile;
$dsn=sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',$c['host'],$c['port']??'3306',$c['database'],$c['charset']??'utf8mb4');
$db=new PDO($dsn,$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function seedUser(PDO $db,string $name,string $email,string $password,string $role,string $phone=''): int { $q=$db->prepare('SELECT user_id FROM users WHERE email=?');$q->execute([$email]);$id=$q->fetchColumn();if($id)return (int)$id;$q=$db->prepare('INSERT INTO users(name,email,password_hash,role,phone) VALUES(?,?,?,?,?)');$q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role,$phone]);return (int)$db->lastInsertId(); }
$admin=seedUser($db,'Admin InProsper','admin@inprosper.test','Admin123!','admin');
$merchantUser=seedUser($db,'Merchant Demo','merchant@inprosper.test','Merchant123!','merchant','081234567890');
$consumer=seedUser($db,'Konsumen Demo','consumer@inprosper.test','Consumer123!','konsumen','081299998888');
$q=$db->prepare('SELECT merchant_id FROM merchants WHERE user_id=?');$q->execute([$merchantUser]);$merchant=$q->fetchColumn();
if(!$merchant){$q=$db->prepare('INSERT INTO merchants(user_id,verified_by_user_id,business_name,business_address,business_category,verification_status) VALUES(?,?,?,?,?,?)');$q->execute([$merchantUser,$admin,'Dapur Berkah','Balikpapan, Kalimantan Timur','Warung makan','disetujui']);$merchant=(int)$db->lastInsertId();}
$q=$db->prepare('SELECT COUNT(*) FROM products WHERE merchant_id=?');$q->execute([$merchant]);
if((int)$q->fetchColumn()===0){$s=$db->prepare('INSERT INTO products(merchant_id,name,description,category,normal_price,discount_percent,stock,expiry_date,expiry_time,is_active) VALUES(?,?,?,?,?,?,?,?,?,1)');$items=[['Nasi Ayam Sambal','Porsi makan lengkap, surplus produksi hari ini.','Makanan berat',30000,40,8,date('Y-m-d',strtotime('+1 day')),'20:00:00'],['Roti Manis Campur','Roti pilihan dari produksi hari ini.','Roti & bakery',18000,50,12,date('Y-m-d',strtotime('+1 day')),'18:00:00'],['Paket Buah Segar','Paket buah yang masih segar dan layak konsumsi.','Buah & sayur',25000,35,5,date('Y-m-d',strtotime('+2 days')),'21:00:00'],['Snack Box','Paket camilan surplus untuk menemani hari.','Camilan',22000,30,6,date('Y-m-d',strtotime('+1 day')),'19:00:00']];foreach($items as $x)$s->execute([$merchant,...$x]);}
echo "Seed selesai.\nAdmin: admin@inprosper.test / Admin123!\nMerchant: merchant@inprosper.test / Merchant123!\nKonsumen: consumer@inprosper.test / Consumer123!\n";
