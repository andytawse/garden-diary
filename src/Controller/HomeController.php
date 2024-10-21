<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\CropsByMonth;
use App\Entity\Crop;

class HomeController extends AbstractController
{
	#[Route('/')]
	public function home(CropsByMonth $cropsByMonth)
	{
		return $this->render('home.html.twig', [
			'crops' => array_map(function (Crop $crop) { 
					return $crop->title; 
				}, $cropsByMonth->getCropsToPlantInCurrentMonth())
		]);
	}
}
