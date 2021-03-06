<?php
session_start();
include 'connect.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
}

if($_SESSION['pengguna']['is_admin'] == '1'){
    header('Location:login.php');
}

$sth = $db->prepare("SELECT * FROM kriteria WHERE parent_id is NULL ORDER BY id");
$sth->execute();
$listKriteria = $sth->fetchAll (PDO::FETCH_ASSOC);

$sth = $db->prepare("SELECT * FROM bobot WHERE periode_id = ? ORDER BY kriteria_id, kriteria_pembanding_id");
$sth->execute(array($_GET['periode_id']));
$listBobot = $sth->fetchAll (PDO::FETCH_ASSOC);

if(isset($_POST['submit'])) {
	$db->beginTransaction();
	$sth = $db->prepare("INSERT INTO periode(nama) VALUES(?)");
	$sth->execute(array($_POST['nama_periode']));
	$periodeId = $db->lastInsertId();
	foreach($_POST['bobot'] as $kriteria_id => $kriteria){
		foreach($kriteria as $pembanding_id => $nilai){ 
			$sth = $db->prepare("INSERT INTO bobot(
                                        periode_id, kriteria_id, kriteria_pembanding_id, nilai)
                                    values(?, ?, ?, ?)");
			$sth->execute(array($periodeId, $kriteria_id, $pembanding_id, $nilai));
		}
	}
	$saved=true;
	$db->commit();
}
?>

<?php include 'header.php'?>
<div class="body" align="center">
    <h1 align="center">Pembobotan Kriteria Balance Scorecard</h1>
	<?php if(isset($saved)):?>
	<div class="message">
		Data Tersimpan!
	</div>
	<?php endif?>
	&nbsp;
	<form method="post">
		<table>
			<tr>
				<td valign="center" class="name">
					<label for="Periode">Nama Periode</label>
				</td>
				<td valign="top" class="value">
					<input type="text" name="nama_periode"/>
				</td>
			</tr>	
		</table>
		<br/>
		<br/>
		<div class="dialog">
			<table border="0">
				<tr>
					<td>Perspektif</td>
					<?php foreach($listKriteria as $kriteria):?>
					<td><?php echo $kriteria['nama'] ?></td>
s					<?php endforeach ?>
				</tr>
				<?php foreach($listKriteria as $baris):?>
				<tr>
					<td><?php echo $baris['nama'] ?></td>
					<?php foreach($listKriteria as $kolom):?>
					<td>
						<select name="bobot[<?php echo $baris['id'] ?>][<?php echo $kolom['id'] ?>]" id="bobot_<?php echo $baris['id']?>_<?php echo $kolom['id']?>">
							<option value="9">9</option>
							<option value="8">8</option>
							<option value="7">7</option>
							<option value="6">6</option>
							<option value="5">5</option>
							<option value="4">4</option>
							<option value="3">3</option>
							<option value="2">2</option>
							<option value="1">1</option>
							<option value="0.50">1/2</option>
							<option value="0.34">1/3</option>
							<option value="0.25">1/4</option>
							<option value="0.20">1/5</option>
							<option value="0.17">1/6</option>
							<option value="0.14">1/7</option>
							<option value="0.12">1/8</option>
							<option value="0.11">1/9</option>
						</select>
					</td>
					<?php endforeach ?>
				</tr>
				<?php endforeach ?>
			</table>
			<br/>
			<?php foreach($listKriteria as $parent):?>
			<?php 
			$sth = $db->prepare("SELECT * FROM kriteria WHERE parent_id = :id");
			$sth->execute(array('id' => $parent['id']));
			$listChild = $sth->fetchAll(PDO::FETCH_ASSOC);
			?>
			
			<?php if (count($listChild)>0):?>

			<div class="body" align="center">
				<h1 align="center">Pembobotan Kriteria Perspektif <?php echo $parent['nama'] ?></h1>
				<table border="0">
					<tr>
						<td>Perspektif</td>
						<?php foreach($listChild as $kriteria):?>
						<td><?php echo $kriteria['nama'] ?></td>
						<?php endforeach ?>
					</tr>
					<?php foreach($listChild as $baris):?>
					<tr>
						<td><?php echo $baris['nama'] ?></td>
						<?php foreach($listChild as $kolom):?>
						<td>
							<select name="bobot[<?php echo $baris['id'] ?>][<?php echo $kolom['id'] ?>]" id="bobot_<?php echo $baris['id']?>_<?php echo $kolom['id']?>">
							<option value="9">9</option>
							<option value="8">8</option>
							<option value="7">7</option>
							<option value="6">6</option>
							<option value="5">5</option>
							<option value="4">4</option>
							<option value="3">3</option>
							<option value="2">2</option>
							<option value="1">1</option>
							<option value="0.50">1/2</option>
							<option value="0.34">1/3</option>
							<option value="0.25">1/4</option>
							<option value="0.20">1/5</option>
							<option value="0.17">1/6</option>
							<option value="0.14">1/7</option>
							<option value="0.12">1/8</option>
							<option value="0.11">1/9</option>
							</select>
						</td>
						<?php endforeach ?>
					</tr>
					<?php endforeach ?>
				</table>
			</div>
			<?php endif ?>
			<?php endforeach ?>
			<div class="buttons" align="center">
	            <span class="button"><input class="save" name="submit" type="submit" value="Create" /></span>
	        </div>
		</div>
	</form>
</div>
<?php include 'footer.php' ?>