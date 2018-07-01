<?php
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
/////// SISFOKOL SLTP v2.0 							///////
/////// (Sistem Informasi Sekolah untuk SLTP v2.0) 	///////
///////////////////////////////////////////////////////////
/////// Dibuat oleh : 								///////
/////// Agus Muhajir, S.Kom 						///////
/////// URL 	: http://sisfokol.wordpress.com 	///////
/////// E-Mail	: hajirodeon@yahoo.com 				///////
/////// HP/SMS	: 081-829-88-54 					///////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////


session_start();

//ambil nilai
require("../../inc/config.php"); 
require("../../inc/fungsi.php"); 
require("../../inc/koneksi.php"); 
require("../../inc/cek/admks.php"); 
$tpl = LoadTpl("../../template/index.html"); 

nocache;

//nilai
$filenya = "pass.php";
$diload = "document.formx.passlama.focus();";
$judul = "Ganti Password";
$judulku = "[$ks_session : $nip4_session.$nm4_session] ==> $judul";
$juduli = $judul;


//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//simpan	
if ($HTTP_POST_VARS['btnSMP'])
	{
	//ambil nilai
	$passlama = md5(nosql($_POST["passlama"]));
	$passbaru = md5(nosql($_POST["passbaru"]));
	$passbaru2 = md5(nosql($_POST["passbaru2"]));
	
	//cek null
	if ((empty($passlama)) OR (empty($passbaru)) OR (empty($passbaru2)))
		{
		$pesan = "Input Tidak Lengkap. Harap Diulangi...!!";
		pekem($pesan,$filenya);
		}
	//nek pass baru, gak sama.
	else if ($passbaru != $passbaru2)
		{
		$pesan = "Password Baru Tidak Sama. Harap Diulangi...!!";
		pekem($pesan,$filenya);
		}
	else
		{
		//query
		$q = mysql_query("SELECT * FROM admin_ks ".
							"WHERE kd = '$kd4_session' ".
							"AND usernamex = '$username4_session' ".
							"AND passwordx = '$passlama'");
		$row = mysql_fetch_assoc($q);
		$total = mysql_num_rows($q);
	
		//cek	
		if ($total != 0) 
			{
			//perintah SQL
			mysql_query("UPDATE admin_ks SET password = '$passbaru' ".
							"WHERE kd = '$kd4_session' ".
							"AND usernamex = '$username4_session'");
				
			//auto-kembali
			$pesan = "PASSWORD BERHASIL DIGANTI.";
			$ke = "../index.php";
			pekem($pesan, $ke);
			} 	
		else 
			{	
			$pesan = "PASSWORD LAMA TIDAK COCOK. HARAP DIULANGI...!!!";
			pekem($pesan, $filenya);
			}
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//isi *START
ob_start();

//menu
require("../../inc/menu/admks.php");
xheadline($judul);

//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<form action="'.$filenya.'" method="post" name="formx">
<p>Password Lama : <br>
<input name="passlama" type="password" size="15" maxlength="15">
</p>
<p>Password Baru : <br>
<input name="passbaru" type="password" size="15" maxlength="15">
</p>
<p>RE-Password Baru : <br>
<input name="passbaru2" type="password" size="15" maxlength="15">
</p>
<p> 
<input name="btnSMP" type="submit" value="SIMPAN">
<input name="btnBTL" type="reset" value="BATAL">
</p>
</form>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");
?>