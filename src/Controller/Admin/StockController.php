<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use App\Entity\Company;
use App\Entity\ExchangeMarket;
use App\Entity\Type;
use App\Form\StockType;
use App\Repository\StockRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage stock contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin/stock")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Saeed Ahmed <saeed.sas@gmail.com>
 */
class StockController extends AbstractController
{

    /**
     * add a new exchange entity.
     *
     * @Route("/new", name="admin_stock_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(Request $request): Response
    {
        $stock = new Stock();
        $stock->setAuthor($this->getUser());

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(StockType::class, $stock)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $company = $em->getRepository(Company::class)->findOneById($request->request->get('stock')['company']);
            $stock->setCompany($company);

            $exchangeMarket = $em->getRepository(ExchangeMarket::class)->findOneById($request->request->get('stock')['exchangeMarket']);
            $stock->setExchangeMarket($exchangeMarket);

            $type = $em->getRepository(Type::class)->findOneById($request->request->get('stock')['type']);
            $stock->setType($type);
            
            $em->persist($stock);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'stock.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_stock_new');
            }

            return $this->redirectToRoute('admin_dashboard_index');
        }

        return $this->render('admin/stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }
}