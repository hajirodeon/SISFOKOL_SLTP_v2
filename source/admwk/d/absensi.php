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
require("../../inc/cek/admwk.php");
$tpl = LoadTpl("../../template/index.html"); 


nocache;

//nilai
$filenya = "absensi.php";
$s = nosql($_REQUEST['s']);
$tapelkd = nosql($_REQUEST['tapelkd']);
$kelkd = nosql($_REQUEST['kelkd']);
$rukd = nosql($_REQUEST['rukd']);
$swkd = nosql($_REQUEST['swkd']);
$skkd = nosql($_REQUEST['skkd']);
$ubln = nosql($_REQUEST['ubln']);
$uthn = nosql($_REQUEST['uthn']);
$ke = "$filenya?tapelkd=$tapelkd&kelkd=$kelkd&".
		"rukd=$rukd&swkd=$swkd&ubln=$ubln&uthn=$uthn";
		

//siswa ne
$qsiw = mysql_query("SELECT * FROM m_siswa ".
						"WHERE kd = '$swkd'");
$rsiw = mysql_fetch_assoc($qsiw);
$siw_nis = nosql($rsiw['nis']);
$siw_nama = balikin($rsiw['nama']);


//judul
$judul = "Absensi Siswa : ($siw_nis).$siw_nama";
$judulku = "[$wk_session : $nip3_session.$nm3_session] ==> $judul";
$judulx = $judul;



//onload
if (empty($ubln))
	{
	$diload = "document.formx.ubln.focus();";
	}
else if (empty($uthn))
	{
	$diload = "document.formx.uthn.focus();";
	}
	



//isi *START
ob_start();

//js
require("../../inc/js/jumpmenu.js");
require("../../inc/js/swap.js");
require("../../inc/menu/admwk.php");


//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<table>
<tr>
<td>';
xheadline($judul);
echo '</td>
<td>
[<a href="detail.php?tapelkd='.$tapelkd.'&kelkd='.$kelkd.'&rukd='.$rukd.'" title="Daftar Siswa">Daftar Siswa</a>]
</td>
</table>';


//tapel
$qpel = mysql_query("SELECT * FROM m_tapel ".
						"WHERE kd = '$tapelkd'");
$rpel = mysql_fetch_assoc($qpel);
$pel_thn1 = nosql($rpel['tahun1']);
$pel_thn2 = nosql($rpel['tahun2']);

//kelas
$qkel = mysql_query("SELECT * FROM m_kelas ".
						"WHERE kd = '$kelkd'");
$rkel = mysql_fetch_assoc($qkel);
$kel_kelas = nosql($rkel['kelas']);


//ruang
$qru = mysql_query("SELECT * FROM m_ruang ".
						"WHERE kd = '$rukd'");
$rru = mysql_fetch_assoc($qru);
$ru_ruang = balikin($rru['ruang']);



echo '<table bgcolor="'.$warnaover.'" width="100%" cellspacing="0" cellpadding="3">
<tr valign="top">
<td>
<strong>Tahun Pelajaran :</strong> '.$pel_thn1.'/'.$pel_thn2.', 
<strong>Kelas :</strong> '.$kel_kelas.',
<strong>Ruang :</strong> '.$ru_ruang.'
</td>
</tr>
</table>

<table bgcolor="'.$warna02.'" width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>
<strong>Bulan : </strong>';
echo "<select name=\"ubln\" onChange=\"MM_jumpMenu('self',this,0)\">";
echo '<option value="'.$ubln.'">'.$arrbln[$ubln].'</option>';
for ($ibln=1;$ibln<=12;$ibln++)
	{
	echo '<option value="'.$filenya.'?tapelkd='.$tapelkd.'&kelkd='.$kelkd.'&'.
			'rukd='.$rukd.'&swkd='.$swkd.'&skkd='.$skkd.'&ubln='.$ibln.'">'.$arrbln[$ibln].'</option>';
	}
echo '</select>';

//tahun
echo "<select name=\"uthn\" onChange=\"MM_jumpMenu('self',this,0)\">";
echo '<option value="'.$uthn.'">'.$uthn.'</option>';
for ($ithn=$pel_thn1;$ithn<=$pel_thn2;$ithn++)
	{
	echo '<option value="'.$filenya.'?tapelkd='.$tapelkd.'&kelkd='.$kelkd.'&rukd='.$rukd.''.
				'&swkd='.$swkd.'&skkd='.$skkd.'&ubln='.$ubln.'&uthn='.$ithn.'">'.$ithn.'</option>';
	}
echo '</select>
</td>
</tr>
</table>
<br>';
	

if (empty($_REQUEST['ubln']))
	{
	echo '<font color="#FF0000"><strong>BULAN Belum Dipilih...!</strong></font>';
	}
else if (empty($_REQUEST['uthn']))
	{
	echo '<font color="#FF0000"><strong>TAHUN Belum Dipilih...!</strong></font>';
	}
else
	{
	echo '<table width="550" border="1" cellspacing="0" cellpadding="3">
	<tr bgcolor="'.$warnaheader.'">
    <td width="30"><strong>Tgl.</strong></td>
	<td width="75"><strong>Hari</strong></td>
	<td width="75"><strong>Jam</strong></td>
	<td width="100"><strong>Ket.</strong></td>
	<td><strong>Keperluan</strong></td>
	</tr>';
	
	//mendapatkan jumlah tanggal maksimum dalam suatu bulan
	$tgl = 0;
	$bulan = $ubln;
	$bln = $bulan + 1;
	$thn = $uthn;

	$lastday = mktime (0,0,0,$bln,$tgl,$thn);

	//total tanggal dalam sebulan
	$tkhir = strftime ("%d", $lastday);

	//lopping tgl
	for ($i=1;$i<=$tkhir;$i++)
		{
		//ketahui harinya
		$day = $i; 
		$month = $bulan; 
		$year = $thn;


		//mencari hari
		$a = substr($year, 2);
			//mengambil dua digit terakhir tahun	
		
		$b = (int)($a/4);
			//membagi tahun dengan 4 tanpa memperhitungkan sisa
	
		$c = $month;
			//mengambil angka bulan
		
		$d = $day;
			//mengambil tanggal
	
		$tot1 = $a + $b + $c + $d;
			//jumlah sementara, sebelum dikurangani dengan angka kunci bulan
	
		//kunci bulanan
		if ($c == 1) 
			{
			$kunci = "2";
			}
		
		else if ($c == 2)
			{
			$kunci = "7";
			}
		
		else if ($c == 3)
			{
			$kunci = "1";
			}
	
		else if ($c == 4)
			{
			$kunci = "6";
			}
	
		else if ($c == 5)
			{
			$kunci = "5";
			}
	
		else if ($c == 6)
			{
			$kunci = "3";
			}
	
		else if ($c == 7)
			{
			$kunci = "2";
			}
	
		else if ($c == 8)
			{
			$kunci = "7";
			}
	
		else if ($c == 9)
			{
			$kunci = "5";
			}
	
		else if ($c == 10)
			{
			$kunci = "4";
			}
	
		else if ($c == 11)
			{
			$kunci = "2";
			}
	
		else if ($c == 12)
			{
			$kunci = "1";
			}
		
		$total = $tot1 - $kunci;
	
		//angka hari
		$hari = $total%7;
	
		//jika angka hari == 0, sebenarnya adalah 7.
		if ($hari == 0)
			{
			$hari = ($hari +7);
			}
	
		//kabisat, tahun habis dibagi empat alias tanpa sisa
		$kabisat = (int)$year % 4;
		
		if ($kabisat ==0)
			{
			$hri = $hri-1;
			}
	


		//hari ke-n 
		if ($hari == 3)
			{
			$hri = 4;
			$dino = "Rabu";
			}
		
		else if ($hari == 4)
			{
			$hri = 5;
			$dino = "Kamis";
			}
	
		else if ($hari == 5)
			{
			$hri = 6;
			$dino = "Jum'at";
			}
		
		else if ($hari == 6)
			{
			$hri = 7;
			$dino = "Sabtu";
			}
	
		else if ($hari == 7)
			{
			$hri = 1;
			$dino = "Minggu";
			}
	
		else if ($hari == 1)
			{
			$hri = 2;
			$dino = "Senin";
			}
	
		else if ($hari == 2)
			{
			$hri = 3;
			$dino = "Selasa";
			}


		//nek minggu, abang ngi wae
		if ($hri == 1)
			{
			$warna = "red";
			$mggu_attr = "disabled";
			}
		else
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				$mggu_attr = "";
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				$mggu_attr = "";
				}
			}

		
		//nilai data
		$qdtf = mysql_query("SELECT * FROM siswa_absensi ".
								"WHERE kd_siswa_kelas = '$skkd' ".
								"AND round(DATE_FORMAT(tgl, '%d')) = '$i' ".
								"AND round(DATE_FORMAT(tgl, '%m')) = '$ubln' ".
								"AND round(DATE_FORMAT(tgl, '%Y')) = '$uthn'");
		$rdtf = mysql_fetch_assoc($qdtf);
		$dtf_perlu = balikin($rdtf['keperluan']);
		$dtf_abskd = balikin($rdtf['kd_absensi']);
		$dtf_jam_xjam = substr($rdtf['jam'],0,2);
		$dtf_jam_xmnt = substr($rdtf['jam'],3,2);
		
		//nek empty
		if ($dtf_jam_xjam == "00")
			{
			$dtf_jam_xjam = "";
			
			if ($dtf_jam_xmnt == "00")
				{
				$dtf_jam_xmnt = "";
				}
			}	
		


		echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
		echo '<td>'.$i.'.</td>
		<td>'.$dino.'</td>
		<td>'.$dtf_jam_xjam.':'.$dtf_jam_xmnt.'
		</td>
		<td>';
		
		//absensinya
		$qbein = mysql_query("SELECT * FROM m_absensi ".
								"WHERE kd = '$dtf_abskd'");
		$rbein = mysql_fetch_assoc($qbein);
		$bein_kd = nosql($rbein['kd']);
		$bein_abs = balikin($rbein['absensi']);
				
		echo $bein_abs;
		echo '</td>
		<td>'.$dtf_perlu.'
		</td>
		</tr>';
		}
	
	echo '</table>
	<input name="tapelkd" type="hidden" value="'.$tapelkd.'">
	<input name="kelkd" type="hidden" value="'.$kelkd.'">
	<input name="rukd" type="hidden" value="'.$rukd.'">
	<input name="swkd" type="hidden" value="'.$swkd.'">
	<input name="ubln" type="hidden" value="'.$ubln.'">
	<input name="uthn" type="hidden" value="'.$uthn.'">';
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