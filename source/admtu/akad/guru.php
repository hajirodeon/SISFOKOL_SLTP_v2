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

//fungsi - fungsi
require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/class/paging.php");
require("../../inc/cek/admtu.php");
$tpl = LoadTpl("../../template/index.html"); 


nocache;

//nilai
$filenya = "guru.php";
$judul = "Guru";
$judulku = "[$tu_session : $nip5_session.$nm5_session] ==> $judul";
$judulx = $judul;
$tapelkd = nosql($_REQUEST['tapelkd']);
$kelkd = nosql($_REQUEST['kelkd']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}
	
$ke = "$filenya?tapelkd=$tapelkd&kelkd=$kelkd&page=$page";





//focus...
if (empty($tapelkd))
	{
	$diload = "document.formx.tapel.focus();";
	}
else if (empty($kelkd))
	{
	$diload = "document.formx.kelas.focus();";
	}
	
	




//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//jika batal
if ($HTTP_POST_VARS['btnBTL'])
	{
	//re-direct
	xloc($ke);
	}


//jika hapus
if ($HTTP_POST_VARS['btnHPS'])
	{
	$tapelkd = nosql($_POST['tapelkd']);
	$kelkd = nosql($_POST['kelkd']);
	$jml = nosql($_POST['jml']);

	//ambil semua
	for ($i=1; $i<=$jml;$i++) 
		{
		//ambil nilai
		$yuk = "item";
		$yuhu = "$yuk$i";
		$kdix = nosql($_POST["$yuhu"]);
	
		//del
		mysql_query("DELETE FROM m_guru ".
						"WHERE kd_tapel = '$tapelkd' ".
						"AND kd_kelas = '$kelkd' ".
						"AND kd = '$kdix'");		
		}
	
	//re-direct
	xloc($ke);
	}



//jika tambah
if ($HTTP_POST_VARS['btnTBH'])
	{
	//nilai
	$tapelkd = nosql($_POST['tapelkd']);
	$kelkd = nosql($_POST['kelkd']);
	$pegawai = nosql($_POST['pegawai']);
	
	
	//nek nul
	if (empty($pegawai))
		{
		$pesan = "Pegawai untuk dijadikan Guru, Belum Dipilih. Harap Diperhatikan...!";
		pekem($pesan,$ke);
		}
	else
		{
		//deteksi
		$qcc = mysqL_query("SELECT m_guru.*, m_pegawai.* ".
								"FROM m_guru, m_pegawai ".
								"WHERE m_guru.kd_pegawai = m_pegawai.kd ".
								"AND m_guru.kd_pegawai = '$pegawai' ".
								"AND m_guru.kd_tapel = '$tapelkd' ".
								"AND m_guru.kd_kelas = '$kelkd'");
		$rcc = mysql_fetch_assoc($qcc);
		$tcc = mysql_num_rows($qcc);
		$cc_nip = nosql($rcc['nip']);
		$cc_nm = balikin($rcc['nama']);
		
		
		//nek iya
		if ($tcc != 0)
			{
			$pesan = "Pegawai dengan NIP : $cc_nip [$cc_nm], Telah Menjadi Guru di Kelas ini. Silahkan Diganti...!";
			pekem($pesan,$ke);
			}
		else
			{
			//query
			mysql_query("INSERT INTO m_guru(kd, kd_tapel, kd_kelas, kd_pegawai) VALUES ".
							"('$x', '$tapelkd', '$kelkd', '$pegawai')");
			
			//re-direct
			xloc($ke);		
			}		
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//isi *START
ob_start();

//js
require("../../inc/js/jumpmenu.js");
require("../../inc/js/swap.js");
require("../../inc/js/checkall.js");
require("../../inc/js/number.js");
require("../../inc/menu/admtu.php");
xheadline($judul);

echo '<form name="formx" method="post" action="'.$filenya.'">
<table bgcolor="'.$warnaover.'" width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>
Tahun Pelajaran : ';
echo "<select name=\"tapel\" onChange=\"MM_jumpMenu('self',this,0)\">";

//terpilih
$qtpx = mysql_query("SELECT * FROM m_tapel ".
						"WHERE kd = '$tapelkd'");
$rowtpx = mysql_fetch_assoc($qtpx);
$tpx_kd = nosql($rowtpx['kd']);
$tpx_thn1 = nosql($rowtpx['tahun1']);
$tpx_thn2 = nosql($rowtpx['tahun2']);

echo '<option value="'.$tpx_kd.'">'.$tpx_thn1.'/'.$tpx_thn2.'</option>';

$qtp = mysql_query("SELECT * FROM m_tapel ".
						"WHERE kd <> '$tapelkd' ".
						"ORDER BY tahun1 ASC");
$rowtp = mysql_fetch_assoc($qtp);
				
do
	{
	$tpkd = nosql($rowtp['kd']);
	$tpth1 = nosql($rowtp['tahun1']);
	$tpth2 = nosql($rowtp['tahun2']);

	echo '<option value="'.$filenya.'?tapelkd='.$tpkd.'">'.$tpth1.'/'.$tpth2.'</option>';
	}
while ($rowtp = mysql_fetch_assoc($qtp));

echo '</select>, 
Kelas : ';
echo "<select name=\"kelas\" onChange=\"MM_jumpMenu('self',this,0)\">";

//terpilih
$qbtx = mysql_query("SELECT * FROM m_kelas ".
						"WHERE kd = '$kelkd'");
$rowbtx = mysql_fetch_assoc($qbtx);

$btxkd = nosql($rowbtx['kd']);
$btxkelas = nosql($rowbtx['kelas']);

echo '<option value="'.$btxkd.'">'.$btxkelas.'</option>';

$qbt = mysql_query("SELECT * FROM m_kelas ".
						"WHERE kd <> '$kelkd' ".
						"ORDER BY no ASC");
$rowbt = mysql_fetch_assoc($qbt);
				
do
	{
	$btkd = nosql($rowbt['kd']);
	$btkelas = nosql($rowbt['kelas']);
	
	echo '<option value="'.$filenya.'?tapelkd='.$tapelkd.'&kelkd='.$btkd.'">'.$btkelas.'</option>';
	}
while ($rowbt = mysql_fetch_assoc($qbt));

echo '</select> 
</td>
</tr>
</table>
<br>';


//nek blm
if (empty($tapelkd))
	{
	echo '<strong><font color="#FF0000">TAHUN PELAJARAN Belum Dipilih...!</font></strong>';
	}
else if (empty($kelkd))
	{
	echo '<strong><font color="#FF0000">KELAS Belum Dipilih...!</font></strong>';
	}
else 
	{
	//penambahan
	echo '<select name="pegawai">
	<option value="" selected>-Pegawai-</option>';
	
	//data pegawai
	$qpeg = mysql_query("SELECT * FROM m_pegawai ".
							"ORDER By nip ASC");
	$rpeg = mysql_fetch_assoc($qpeg);
	
	do
		{
		$peg_kd = nosql($rpeg['kd']);
		$peg_nip = nosql($rpeg['nip']);
		$peg_nm = nosql($rpeg['nama']);
				
		echo '<option value="'.$peg_kd.'">'.$peg_nip.'. '.$peg_nm.'</option>';
		}
	while ($rpeg = mysql_fetch_assoc($qpeg));
	
	
	echo '</select>
	
	<input name="btnTBH" type="submit" value="Tambah >>">';
	
	
	//query
	$p = new Pager();
	$start = $p->findStart($limit);
	
	$sqlcount = "SELECT m_guru.*, m_guru.kd AS mgkd, m_pegawai.*  ".
					"FROM m_guru, m_pegawai ".
					"WHERE m_guru.kd_pegawai = m_pegawai.kd ".
					"AND m_guru.kd_tapel = '$tapelkd' ".
					"AND m_guru.kd_kelas = '$kelkd' ".
					"ORDER BY m_pegawai.nip ASC";
	$sqlresult = $sqlcount;
			
	$count = mysql_num_rows(mysql_query($sqlcount));
	$pages = $p->findPages($count, $limit);
	$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
	$pagelist = $p->pageList($_GET['page'], $pages, $target);
	$data = mysql_fetch_array($result);

	//detail
	echo '<table width="600" border="1" cellspacing="0" cellpadding="3">
	<tr bgcolor="'.$warnaheader.'">
	<td width="1%">&nbsp;</td>
	<td width="100"><strong><font color="'.$warnatext.'">NIP</font></strong></td>
	<td><strong><font color="'.$warnatext.'">Nama</font></strong></td>
	</tr>';

	if ($count != 0)
		{
		do 
			{ 
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}
			
		
			//nilai
			$nomer = $nomer + 1;
			$kd = nosql($data['mgkd']);
			$nip = nosql($data['nip']);
			$nama = balikin($data['nama']);
		
		
			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td><input name="kd'.$nomer.'" type="hidden" value="'.$kd.'">
			<input type="checkbox" name="item'.$nomer.'" value="'.$kd.'"> 
	        </td>
			<td>'.$nip.'</td>
			<td>'.$nama.'</td>
			</tr>';			
			} 
		while ($data = mysql_fetch_assoc($result)); 
		}

	echo '</table>
	<table width="600" border="0" cellspacing="0" cellpadding="3">
	<tr> 
	<td width="250">
	<input name="tapelkd" type="hidden" value="'.$tapelkd.'">
	<input name="kelkd" type="hidden" value="'.$kelkd.'">
	<input name="jml" type="hidden" value="'.$limit.'"> 
	<input name="btnALL" type="button" value="SEMUA" onClick="checkAll('.$limit.')"> 
	<input name="btnBTL" type="reset" value="BATAL"> 
	<input name="btnHPS" type="submit" value="HAPUS"> 
	</td>
	<td>
	<div align="right"><strong><font color="#FF0000">'.$count.'</font></strong> Data. '.$pagelist.'</div>
	</td>
	</tr>
	</table>';	
	}
	
echo '</form>
<br>
<br>
<br>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//isi
$isi = ob_get_contents();
ob_end_clean();


require("../../inc/niltpl.php");
?>