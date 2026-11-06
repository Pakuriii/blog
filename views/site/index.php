<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">


    <div class="body-content">
	<?php
	foreach($data as $row){
		echo Html::tag('h1', $row->title);
		echo Html::tag('p',Html::tag('small', $row->username . ' ' . $row->date));
		echo $row->content;
		echo '<hr/>';	
	}
	
	?>

    </div>
</div>
