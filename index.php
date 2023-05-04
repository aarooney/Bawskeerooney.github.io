<?php
$context = stream_context_create(array(
    'http' => array(
        'header' => 'Accept: application/xml'
    )
));
$url = 'http://dotsplace.com/com494S23/xml/plants.xml';
// http://dotsplace.com/com494S23/xml/plants_2.xml
$xml = file_get_contents($url);
$xml = simplexml_load_string($xml);

$filter_zone_arr = array();
$filter_light_arr = array();

$data_array = array();

foreach ($xml->children() as $v) {

    $zones = strval($v->{'ZONE'}[0]);
    $light = strval($v->{'LIGHT'}[0]);

    if (! in_array($zones, $filter_zone_arr)) {
        $filter_zone_arr[] = $zones;
    }

    if (! in_array($light, $filter_light_arr)) {
        $filter_light_arr[] = $light;
    }
    
    $data_array[] = (array) $v;
}

$botanical  = array_column($data_array, 'BOTANICAL');
$price = array_column($data_array, 'PRICE');




$get_sortby = !empty($_GET['sort_by']) ? $_GET['sort_by'] : '';
$get_zone = !empty($_GET['zone']) ? $_GET['zone'] : '';
$get_light = !empty($_GET['light']) ? $_GET['light'] : '';

switch($get_sortby) {
    case "1": array_multisort($botanical, SORT_ASC, $data_array); break; // Name Ascending
    case "2": array_multisort($botanical, SORT_DESC, $data_array); break; // Name Descending
    case "3": array_multisort($price, SORT_ASC, $data_array);break; // Price Low to High
    case "4": array_multisort($price, SORT_DESC, $data_array);break; // Price High to Low
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>The Coder</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
	<form method="GET">
		<div class="container mt-5">
			<div class="row">
			<div class="col-4 pb-2">
				<label>Filter by Zone:</label>
				<select class="form-select" name="zone" onchange="this.form.submit()"
					aria-label="Default select example">
					<option value="">Select Zone</option>
  					<?php foreach($filter_zone_arr as $v): ?>
  					<option value="<?php echo $v ?>" <?php if($v== $get_zone){echo "selected";} ?>><?php echo $v ?></option>
  					<?php endforeach;?>
				</select>
			</div>
			<div class="col-4 pb-2"
				<label>Filter by Light:</label>
				<select class="form-select" name="light" onchange="this.form.submit()"
					aria-label="Default select example">
					<option value="">Select Light</option>
  					<?php foreach($filter_light_arr as $v): ?>
  					<option value="<?php echo $v ?>" <?php if($v==$get_light){echo "selected";} ?>><?php echo $v ?></option>
  					<?php endforeach;?>
				</select>
			</div>
			<div class="col-4 pb-2"
				<label>Sort By:</label>
				<select class="form-select" name="sort_by" onchange="this.form.submit()"
					aria-label="Default select example">
					<option value="">Select Sort By</option>
					<option value="1" <?php if($get_sortby=='1'){echo "selected";} ?>>Name Ascending</option>
					<option value="2" <?php if($get_sortby=='2'){echo "selected";} ?>>Name Descending</option>
					<option value="3" <?php if($get_sortby=='3'){echo "selected";} ?>>Price Low to High</option>
					<option value="4" <?php if($get_sortby=='4'){echo "selected";} ?>>Price High to Low</option>
				</select>
			</div>
			</div>
		</div>
	</form>
	<div class="container">
		
		<?php
$k = 0;

foreach ($data_array as $v) {
    if (! empty($_GET['zone'])) {
        if ($_GET['zone'] != $v['ZONE']) {
            continue;
        }
    }
    if (! empty($_GET['light'])) {
        if ($_GET['light'] != $v['LIGHT']) {
            continue;
        }
    }
    if ($k % 3 == 0) {
        echo '<div class="row">';
    }

    echo '<div class="col-4 pb-2">';
    echo '<div class="d-grid gap-2 col-12 mx-auto">';
    echo '    <div class="bg-image"  style=\'background-image: url("images/'.$v['BOTANICAL'].'.jpg");height: 300px;background-size:cover;background-repeat:no-repeat;background-position: center center;\'>&nbsp;</div>';
    echo '    <button type="button" class="btn btn-primary text-center" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="loadFlowerDetail(\''.addslashes($v['COMMON']).'\',\''.$v['BOTANICAL'].'\',\''.$v['PRICE'].'\',\''.$v['AVAILABILITY'].'\',\''.$v['ZONE'].'\',\''.$v['LIGHT'].'\'); ">' . $v['BOTANICAL'] . '</button>';
    echo '</div>';
    echo '</div>';

    if ($k % 3 == 2) {
        echo '</div>';
    }
    $k ++;
}
?>
		
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  		<div class="modal-dialog">
    		<div class="modal-content">
      		<div class="modal-header">
        		<h5 class="modal-title" id="flowerModalLabel"></h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
      		<div class="modal-body">
        		<table>
        			<tr>
        				<td><strong>Common Name : </strong></td><td id="td_common_name"></td>
        			</tr>
        			<tr>
        				<td><strong>Botanical Name : </strong></td><td id="td_botanical_name"></td>
        			</tr>
        			<tr>
        				<td><strong>Zone : </strong></td><td id="td_zone"></td>
        			</tr>
        			<tr>
        				<td><strong>Light : </strong></td><td id="td_light"></td>
        			</tr>
        			<tr>
        				<td><strong>Availability : </strong></td><td id="td_availability"></td>
        			</tr>
        			<tr>
        				<td><strong>Price : </strong></td><td id="td_price"></td>
        			</tr>
        		</table>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      		</div>
    		</div>
  		</div>
	</div>
	
</body>
<script type="text/javascript">
	jQuery( document ).ready(function() {
    	
    	
    	
	});
	
	function loadFlowerDetail(common_name, botanical_name, price, availability, zone , light) {
    		jQuery('#flowerModalLabel').html(common_name);
    		jQuery('#td_common_name').html(common_name);
    		jQuery('#td_botanical_name').html(botanical_name);
    		jQuery('#td_zone').html(zone);
    		jQuery('#td_light').html(light);
    		jQuery('#td_availability').html(availability);
    		jQuery('#td_price').html(price);
    	}
	
    	
</script>
</html>