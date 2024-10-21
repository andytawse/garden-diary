<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	#[Route('/')]
	public function home()
	{
		$month = date('m');
		return $this->render('home.html.twig', [
			'month' => $month
		]);
	}
}
