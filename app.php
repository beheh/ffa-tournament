<?php

use Symfony\Component\HttpFoundation\Request;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use BehEh\Wizard\Entities\Participant;

$app = new Silex\Application();
$app['debug'] = true;
$base = 'http://localhost/~benedict/wizard/';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new DoctrineServiceProvider, array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'dbname' => 'wizard1',
    ),
));

$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__ . '/cache',
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'BehEh\Wizard\Entities',
                'path' => __DIR__ . '/src/Entities',
            )
        ),
    ),
));

/**
 * @var $em \Doctrine\ORM\EntityManager
 */
$em = $app['orm.em'];

/**
 * @var $twig \Twig_Environment
 */
$twig = $app['twig'];

$twig->addGlobal('base', $base);

$app->before(function (Request $request) use ($app, $em) {
	$rounds = $em->getRepository('BehEh\\Wizard\\Entities\\Round')->findBy(
	    array('type' => \BehEh\Wizard\Entities\Round::TYPE_ROUND)
	);

	$round_items = array();
	foreach ($rounds as $round) {
	    $id = $round->getId();
	    $round_items['/groupstage/round-' . $id] = $id . '. Hauptrunde';
	}

	$navbar = array_merge(
	    array(
		'/participants' => 'Teilnehmer',
	    ),
	    $round_items,
	    array(
		'/semifinals' => 'Halbfinale',
		'/finals' => 'Finale',
	    )
	);

	$app['twig']->addGlobal('navbar', $navbar);
});

$app->before(function (Request $request) use ($app) {
    $app['twig']->addGlobal('current_url', strtok($request->getUri(), '?'));
});

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
});

$app->get('/participants', function (Request $request) use ($app, $em) {

    $sorting = array();
    $comparator = null;
    switch ($request->query->get('sort')) {
        case 'name':
            $sorting = array('name' => 'ASC');
            break;
        case 'points':
            $comparator = function(Participant $a, Participant $b) {
                if ($a->getPoints() == $b->getPoints()) {
                    return 0;
                }
                return $a->getPoints() > $b->getPoints() ? -1 : 1;
            };
            break;
        case 'status':
        default:
            $sorting =  array('status' => 'DESC', 'name' => 'ASC');
            break;
    }

    $participants = $em->getRepository('BehEh\\Wizard\\Entities\\Participant')->findBy(
        array(),
        $sorting
    );

    if($comparator) {
        usort($participants, $comparator);
    }

    return $app['twig']->render('participants.html.twig', array(
        'participants' => $participants,
        'sort' => $request->query->get('sort', 'none')
    ));
});

$app->get('/participants/{id}', function ($id) use ($app, $em) {
    $participant = $em->find('BehEh\\Wizard\\Entities\\Participant', $id);
    if (!$participant) {
        $app->abort(404, 'Participant does not exist.');
    }
    return $app['twig']->render('participant.html.twig', array(
        'participant' => $participant
    ));
})->assert('id', '\d+');

$app->get('/groupstage/round-{id}', function ($id) use ($app, $em) {
    $round = $em->find('BehEh\\Wizard\\Entities\\Round', $id);
    if (!$round) {
        $app->abort(404, 'Round does not exist.');
    }
    return $app['twig']->render('groupstage.html.twig', array(
        'round' => $round
    ));
})->assert('id', '\d+');

$app->get('/semifinals', function () use ($app, $em) {
    $round = $em->getRepository('BehEh\\Wizard\\Entities\\Round')->findOneBy(
        array('type' => \BehEh\Wizard\Entities\Round::TYPE_SEMIFINALE)
    );
    if (!$round) {
        $app->abort(404, 'Semifinals round does not exist.');
    }
    return $app['twig']->render('semifinals.html.twig', array(
        'round' => $round
    ));
});

$app->get('/finals', function () use ($app, $em) {
    $round = $em->getRepository('BehEh\\Wizard\\Entities\\Round')->findOneBy(
        array('type' => \BehEh\Wizard\Entities\Round::TYPE_FINALE)
    );
    if (!$round) {
        $app->abort(404, 'Finals round does not exist.');
    }
    return $app['twig']->render('finals.html.twig', array(
        'round' => $round
    ));
});

return $app;
