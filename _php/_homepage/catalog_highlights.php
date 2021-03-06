<?php 
if(!defined('MAIN_DIR')){define('MAIN_DIR',dirname('__FILENAME__'));}
require_once(MAIN_DIR.'/_php/_config/session.php');
require_once(MAIN_DIR.'/_php/_config/connection.php');
require_once(MAIN_DIR.'/_php/_config/functions.php');
confirm_logged_in();

$sql = "SELECT * FROM $DB_NAME.image 
            WHERE catalogued = 1 ";
$result = db_query($mysqli, $sql);
if ($result->num_rows > 0):

    while ($row = $result->fetch_assoc()):
        $highlight_options[] = $row;
    endwhile;

    $random_key = rand(0, count($highlight_options)-1);
    $id = $highlight_options[$random_key]['id'];
    
    $sql = "SELECT image.id, 
                image.legacy_id,
                image.related_works, 
                title.related_works, 
                title.title_text, 
                agent.related_works, 
                agent.agent_text 
                FROM $DB_NAME.image 
                INNER JOIN $DB_NAME.title 
                ON image.id = {$id}
                    AND image.related_works = title.related_works 
                INNER JOIN $DB_NAME.agent 
                ON image.id = {$id} 
                    AND image.related_works = agent.related_works
                LIMIT 1 ";

    $title_res = db_query($mysqli, $sql);

    while ($row = $title_res->fetch_assoc()):

        $title = $row['title_text'];
        $agent = $row['agent_text'];
        if ($row['legacy_id'] != NULL) $filename = $row['legacy_id'];
        else $filename = $id;

    endwhile;

    $title_res->free();

?>

<div id="home_right_pane">

<div id="catalog_highlights">

	<h3>Catalog Highlight</h3>

	<div class="outer_wrapper" hidden>

		<div class="inner_wrapper">

			<img class="highlight_image" src="<?php echo $webroot; ?>/_plugins/timthumb/timthumb.php?src=<?php echo $image_src; echo $filename; ?>.jpg&amp;h=400&amp;q=90">

			<div class="banner">

				<div class="banner_text">
					<span class="title"><?php echo ($title); ?></span><br>
					<span class="agent"><?php echo($agent); ?></span>
				</div>

			</div>

		</div>

	</div>

</div>

<script>

	$('div#catalog_highlights .outer_wrapper').delay(400).fadeIn(800);

</script>

<?php endif; ?>
