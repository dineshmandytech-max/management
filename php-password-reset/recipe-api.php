<?php

class RecipeAPI
{
    private string $apiKey;
    private string $baseUrl = "https://api.spoonacular.com/recipes/complexSearch";
    // private string $ingredientsUrl = "https://api.spoonacular.com/recipes/findByIngredients";
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function search(string $query, int $number = 10): array
    {
        $url = $this->baseUrl . "?" . http_build_query([
            "apiKey" => $this->apiKey,
            "query" => $query,
            "number" => $number,
            "addRecipeInformation" => "true",
            "fillIngredients" => "true",
        ]);
        $response = file_get_contents($url);
        if ($response === false) {
            return [];
        }
        $data = json_decode($response, true);
        return $data["results"] ?? [];
    }

    public function searchByIngredients(string $ingredients, int $number = 10): array
    {
        return $this->search($ingredients, $number);
    }
}

class Recipe
{
    private array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function image(): string
    {
        return $this->data["image"] ?? "";
    }

    public function name(): string
    {
        return $this->data["title"] ?? "N/A";
    }

    public function readyTime(): string
    {
        return isset($this->data["readyInMinutes"])
            ? $this->data["readyInMinutes"] . " min"
            : "N/A";
    }

    public function vegetarian(): string
    {
        return isset($this->data["vegetarian"])
            ? (!empty($this->data["vegetarian"]) ? "Yes" : "No")
            : "N/A";
    }

    public function dishType(): string
    {
        return implode(", ", $this->data["dishTypes"] ?? ["N/A"]);
    }

    public function ingredients(int $limit = 150): string
    {
        $ingredients = $this->data["extendedIngredients"] ?? array_merge(
            $this->data["usedIngredients"] ?? [],
            $this->data["missedIngredients"] ?? []
        );

        $ingredientText = implode(
            ", ",
            array_map(
                fn($item) => $item["original"] ?? $item["name"] ?? "",
                $ingredients
            )
        );

        return strlen($ingredientText) > $limit
            ? substr($ingredientText, 0, $limit) . "..."
            : $ingredientText;
    }

    public function summary(int $limit = 150): string
    {
        $summary = strip_tags($this->data["summary"] ?? "N/A");

        return strlen($summary) > $limit
            ? substr($summary, 0, $limit) . "..."
            : $summary;
    }
}