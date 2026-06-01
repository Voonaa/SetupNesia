<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Get all categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::withCount('products')->get();
    }

    /**
     * Find category by ID.
     *
     * @param int $id
     * @return \App\Models\Category
     */
    public function getCategoryById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return \App\Models\Category
     */
    public function createCategory(array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);
        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param \App\Models\Category $category
     * @param array $data
     * @return \App\Models\Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category safely.
     *
     * @param \App\Models\Category $category
     * @return bool
     * @throws \Exception
     */
    public function deleteCategory(Category $category): bool
    {
        // Safety check: Don't delete if there are active products
        if ($category->products()->count() > 0) {
            throw new \Exception("Cannot delete category because it has active products associated with it.");
        }

        return $category->delete();
    }
}
