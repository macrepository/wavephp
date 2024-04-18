<?php

namespace App\Product\Controller;

use Base\Router\Route;
use Base\Views\Render;
use Base\Validation\Validate;
use Illuminate\Validation\ValidationException;
use App\Product\Model\Product as ProductModel;
use Base\Notification\MessageManager;
use Base\Session\Session;

class Product
{

    const VIEWS = 'Product/view/product';
    const VIEWS_FORM = 'Product/view/product-form';

    protected $validate;
    protected $productModel;
    protected $messageManager;
    protected $session;
    protected $user;

    protected $productRules = [
        'name' => 'required|max:50',
        'category' => 'required|max:50',
        'quantity' => 'required|numeric',
        'price' => 'required|numeric',
    ];

    public function __construct()
    {
        $this->session = new Session();
        $this->user = $this->session->get(Session::USER_KEY);

        if (!$this->user) {
            Route::redirect('/user/login');
        }

        $this->validate = Validate::getInstance()->getValidation();
        $this->productModel = new ProductModel();
        $this->messageManager = new MessageManager();
    }

    public function listProductView()
    {
        return Render::views(self::VIEWS);
    }

    public function addProductView()
    {
        return Render::views(self::VIEWS_FORM, [
            'action' => 'add',
            'url'   => '/product/add'
        ]);
    }

    public function updateProductView($req)
    {
        $productId = $req['id'];
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->messageManager->setErrorMessage("Cannot edit product. ID was not found!");
            return Route::redirect('/product');
        }

        return Render::views(self::VIEWS_FORM, [
            'action' => 'edit',
            'product' => $product,
            'url'   => '/product/edit/' . $productId
        ]);
    }

    public function addProduct($req)
    {
        $product = $req['body'] ?? [];

        try {
            $this->validate->make($product, $this->productRules)->validate();
        } catch (ValidationException $e) {
            $this->messageManager->setErrorMessage($e->getMessage());
            return Route::redirect('/product/add');
        }

        $product['user_id'] = $this->user['id'];

        $isInserted = $this->productModel->create($product);

        // $i = 1;
        // $original = $product;
        // while ($i <= 30) {
        //     $product['name'] = $product['name'] . $i;
        //     $product['category'] = $product['category'] . $i;
        //     $product['quantity'] = $i;
        //     $product['price'] = $i * 100;
        //     $isInserted = $this->productModel->create($product);
        //     $i++;
        //     $product = $original;
        // }

        if (!$isInserted) {
            $this->messageManager->setErrorMessage('There has been an error saving product to the databse!');
            return Route::redirect('/product/add');
        }

        $this->messageManager->setSuccessMessage('Product was successfully added');

        return Route::redirect('/product');
    }

    public function updateProduct($req)
    {
        $product = $req['body'] ?? [];
        $productId = $req['id'];

        try {
            $this->validate->make($product, $this->productRules)->validate();
        } catch (ValidationException $e) {
            return Route::setResponse(Route::BAD_REQUEST, null, $e->getMessage());
            $this->messageManager->setErrorMessage($e->getMessage());
            return Route::redirect('/product/edit');
        }

        $isInserted = $this->productModel->update($productId, $product);

        if (!$isInserted) {
            $this->messageManager->setErrorMessage('There has been an error updating product to the databse!');
            return Route::redirect('/product/edit');
        }

        $this->messageManager->setSuccessMessage('Product was successfully updated');

        return Route::redirect('/product');
    }

    public function getProduct($req)
    {
        $search = $req['query']['search'] ?? '';
        $search = urldecode($search);

        $page = $req['query']['page'] ?? null;
        $limit = $req['query']['limit'] ?? null;
        $userId = $this->user['id'] ?? '';

        $options = [
            'search' => $search,
            'page' => $page,
            'limit' => $limit,
            'userId' => $userId
        ];

        $product = $this->productModel->findAll($options);

        if (!$product) {
            return Route::setResponse(Route::NOT_FOUND, null, "No records were found!");
        }

        $data = [
            'data' => $product,
            'totalCount' => $this->productModel->totalCount($options),
            'limit' => $limit,
            'page'  => $page
        ];

        return Route::setResponse(Route::SUCCESS, $data);
    }

    public function deleteProduct($req)
    {
        $productId = $req['id'] ?? '';

        $isDeleted = $this->productModel->delete($productId);

        if (!$isDeleted) {
            return Route::setResponse(Route::BAD_REQUEST, null, "Product not found!");
        }

        return Route::setResponse(Route::SUCCESS);
    }
}
