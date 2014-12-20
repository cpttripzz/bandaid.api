<?php
namespace ZE\BABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use ZE\BABundle\Entity\Region;
use Faker;
//require_once '../../vendor/fzaninotto/faker/src/autoload.php';
class LocationController extends Controller
{
    public function indexAction(Request $request){
        $map = $this->get('ivory_google_map.map');
//        $result = $this->container
//            ->get('bazinga_geocoder.geocoder')
//            ->geocode($request->server->get('REMOTE_ADDR'));
//
//        $latitude = $result->getLatitude();
        $latitude = 43.6449285;
//        $longitude = $result->getLongitude();
        $longitude = -79.4560727;
        $map->setCenter($latitude, $longitude, true);

        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('ZE\BABundle\Entity\Address')->getClosestAddresses($latitude,$longitude);
        foreach($addresses as $address){
            $latitude = $address['latitude'];
            $longitude = $address['longitude'];
            $marker = new Marker();
            $marker->setPrefixJavascriptVariable('marker_');
            $marker->setPosition($latitude, $longitude, true);
            $marker->setAnimation(Animation::DROP);
            $marker->setOption('clickable', false);
            $marker->setOption('flat', true);
            $marker->setOptions(array(
                'clickable' => false,
                'flat'      => true,
            ));
            $map->addMarker($marker);
        }

        $map->setMapOption('zoom', 10);
        return $this->render(
            'ZEBABundle:Location:index.html.twig',
            array('map' => $map)
        );
    }
    public function geocodeAction(Request $request)
    {
//        $result = $this->container
//            ->get('bazinga_geocoder.geocoder')
//            ->geocode($request->server->get('Toronto, Canada'));
        $geo = $this->get('google_geolocation.geolocation_api');

//        $body = $this->container
//            ->get('bazinga_geocoder.dumper_manager')
//            ->get('geojson')
//            ->dump($result);

        try{
            $em = $this->getDoctrine()->getManager();
            $cities = $em->getRepository('ZE\BABundle\Entity\City')->findAll();
            foreach($cities as $city){
                $cityName = $city->getName() . ', ' .$city->getCountry()->getName();

                $location = $geo->locateAddress($cityName);
                $result = json_decode($location->getResult(),true);
                if (!empty($result[0]['geometry']['location'])){
                    $cityLatitude = $result[0]['geometry']['location']['lat'];
                    $cityLongitude = $result[0]['geometry']['location']['lng'];
                    $city->setLatitude($cityLatitude);
                    $city->setLongitude($cityLongitude);
                    $em->flush();
                }
                foreach ($result[0]['address_components'] as $addressComponent){
                    if (!isset($addressComponent['types'][0])){
                        continue 2;
                    }
                    $type = $addressComponent['types'][0];
                    if ($type == 'administrative_area_level_1'){
                        $regionShortName = $addressComponent['short_name'];
                        $regionLongName = $addressComponent['long_name'];

                        $region = $em->getRepository('ZE\BABundle\Entity\Region')->findOneByLongName($regionLongName);
                        if (!$region){
                            $region = new Region();
                            $region->setCountry($city->getCountry());
                            $region->setShortName($regionShortName);
                            $region->setLongName($regionLongName);
                            $em->persist($region);

                        }
                        $city->setRegion($region);
                        $em->flush();
                    }

                }
            }

        } catch (\Exception $e){
            $response = new Response();
            $response->setContent($e->getMessage());

            return $response;
        }

        $response = new Response();
        $response->setContent($location->getResult());

        return $response;
    }
}