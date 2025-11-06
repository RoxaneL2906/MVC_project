<?php

class ProductModel
{
    private PDO $bdd;
    private PDOStatement $getProducts;
    private PDOStatement $getProduct;
    private PDOStatement $addProduct;
    private PDOStatement $delProduct;
    private PDOStatement $editProduct;

    function __construct()
    {
        $this->bdd = new PDO("mysql:host=bdd;dbname=app-database", "root", "root");

        $this->getProducts = $this->bdd->prepare("SELECT * FROM `Produit` LIMIT :limit");
        $this->getProduct = $this->bdd->prepare("SELECT * FROM `Produit` WHERE id = :id");
        $this->addProduct = $this->bdd->prepare("INSERT INTO Products (name, price, image) VALUES (:name, :price, :image)");
        $this->delProduct = $this->bdd->prepare("DELETE FROM Products WHERE id = :id");
        $this->editProduct = $this->bdd->prepare("UPDATE Products SET name = :name, price = :price, image =:image WHERE id = :id");
    }

    public function getAll(int $limit = 50): array
    {
        $this->getProducts->bindValue("limit", $limit, PDO::PARAM_INT);
        $this->getProducts->execute();
        $rawProducts = $this->getProducts->fetchAll();

        $productsEntity = [];
        foreach ($rawProducts as $rawProduct) {
            $productsEntity[] = new ProductEntity(
                $rawProduct["name"],
                $rawProduct["price"],
                $rawProduct["image"],
                $rawProduct["id"]
            );
        }

        return $productsEntity;
    }

    public function get($id): ProductEntity | NULL
    {
        $this->getProduct->bindValue("id", $id, PDO::PARAM_INT);
        $this->getProduct->execute();
        $rawProduct = $this->getProduct->fetch();

        if (!$rawProduct) {
            return NULL;
        }

        return new ProductEntity(
            $rawProduct["name"],
            $rawProduct["price"],
            $rawProduct["image"],
            $rawProduct["id"]
        );
    }

    public function add(string $name, float $price, string $image): void
    {
        $this->addProduct->bindValue("name", $name, PDO::PARAM_STR);
        $this->addProduct->bindValue("price", $price, PDO::PARAM_STR);
        $this->addProduct->bindValue("image", $image, PDO::PARAM_STR);
        $this->addProduct->execute();
    }

    public function del(int $id): void
    {
        $this->delProduct->bindValue("id", $id, PDO::PARAM_INT);
        $this->delProduct->execute();
    }

    public function edit(
        int $id,
        string $name = NULL,
        float $price = NULL,
        string $image = NULL
    ): ProductEntity | NULL {
        $originalProductEntity = $this->get($id);

        if (!$originalProductEntity) {
            return NULL;
        }

        $this->editProduct->bindValue(
            "name",
            $name ? $name : $originalProductEntity->getName()
        );
        $this->editProduct->bindValue(
            "price",
            $price ? $price : $originalProductEntity->getPrice()
        );
        $this->editProduct->bindValue(
            "image",
            $image ? $image : $originalProductEntity->getImage()
        );

        $this->editProduct->bindValue("id", $id, PDO::PARAM_INT);

        $this->editProduct->execute();

        return $this->get($id);
    }
}


class ProductEntity
{
    private const NAME_MIN_LENGTH = 3;
    private const PRICE_MIN = 0;
    private const DEFAULT_IMG_URL = "/public/images/default.png";

    private $name;
    private $price;
    private $image;
    private $id;

    function __construct(string $name, float $price, string $image, int $id = NULL)
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->setImage($image);
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        if (strlen($name) < $this::NAME_MIN_LENGTH) {
            throw new Error("Name is too short minimum 
            length is " . $this::NAME_MIN_LENGTH);
        }
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price)
    {
        if ($price < 0) {
            throw new Error("Price is too short minimum price is " . $this::PRICE_MIN);
        }
        $this->price = $price;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image)
    {
        if (strlen($image) <= 0) {
            $this->image = $this::DEFAULT_IMG_URL;
        }
        $this->image = $image;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
