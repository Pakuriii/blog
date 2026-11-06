<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RbacController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
	$auth = \Yii::$app->authManager;
        $auth->removeAll();

	$author = $auth->createRole('author');
        $auth->add($author);

	$admin = $auth->createRole('admin');
        $auth->add($admin);
	$auth->addChild($admin, $author);

	$auth->assign($admin, 'admin');
    }
}
