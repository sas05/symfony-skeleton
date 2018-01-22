<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Stock;
use App\Entity\ExchangeMarket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage dashboard contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin/dashboard")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Saeed Ahmed <saeed.sas@gmail.com>
 */
class DashboardController extends AbstractController
{
    /**
     * Dashboard index.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_dashboard_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="admin_index")
     * @Route("/", name="admin_dashboard_index")
     * @Method("GET")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $companyRepo = $em->getRepository(Company::class)->findAll();

        $cards = [];

        foreach($companyRepo as $key => $company) {
            foreach ($company->getStocks() as $index => $stock) {
                $cards[$key]['name'] = $company->getName();
                $cards[$key]['stocks'][$index]['id'] = $stock->getId();
                $cards[$key]['stocks'][$index]['type'] = $stock->getType()->getName();
                $cards[$key]['stocks'][$index]['exchange'] = $stock->getExchangeMarket()->getName();
                $cards[$key]['stocks'][$index]['price'] = $stock->getPrice();
            }
            if(isset($cards[$key]['stocks'])) {
                $cards[$key]['stocks'] = $this->array_group_by($cards[$key]['stocks'], 'type');
            }
        }

        $exchangeRepo = $em->getRepository(ExchangeMarket::class)->findAll();

        $highestStockRepo =  $em->getRepository(Stock::class)->getHighestStock();

        return $this->render('admin/dashboard/index.html.twig',[
            'cards' => $cards,
            'exchanges' => $exchangeRepo,
            'highestStockRepo' => $highestStockRepo,
        ]);
    }

    /**
	 * Groups an array by a given key.
	 *
	 * Groups an array into arrays by a given key, or set of keys, shared between all array members.
	 *
	 * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
	 * This variant allows $key to be closures.
	 *
	 * @param array $array   The array to have grouping performed on.
	 * @param mixed $key,... The key to group or split by. Can be a _string_,
	 *                       an _integer_, a _float_, or a _callable_.
	 *
	 *                       If the key is a callback, it must return
	 *                       a valid key from the array.
	 *
	 *                       If the key is _NULL_, the iterated element is skipped.
	 *
	 *                       ```
	 *                       string|int callback ( mixed $item )
	 *                       ```
	 *
	 * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
	 */
	public function array_group_by(array $array, $key)
	{
		if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
			trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
			return null;
		}
		$func = (!is_string($key) && is_callable($key) ? $key : null);
		$_key = $key;
		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value) {
			$key = null;
			if (is_callable($func)) {
				$key = call_user_func($func, $value);
			} elseif (is_object($value) && isset($value->{$_key})) {
				$key = $value->{$_key};
			} elseif (isset($value[$_key])) {
				$key = $value[$_key];
			}
			if ($key === null) {
				continue;
			}
			$grouped[$key][] = $value;
        }
		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();
			foreach ($grouped as $key => $value) {
				$params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $params);
			}
		}
		return $grouped;
	}
}