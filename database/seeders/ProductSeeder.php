<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // CATEGORÍAS
        // ==========================================
        
        $rosas = Category::create([
            'name' => 'Rosas Eternas',
            'slug' => 'rosas-eternas',
            'description' => 'Hermosos arreglos con rosas eternas que duran para siempre',
            'image' => 'rosas',
            'is_active' => true,
        ]);

        $girasoles = Category::create([
            'name' => 'Girasoles',
            'slug' => 'girasoles',
            'description' => 'Brillantes girasoles para iluminar cualquier día',
            'image' => 'girasoles',
            'is_active' => true,
        ]);

        $mixtos = Category::create([
            'name' => 'Arreglos Mixtos',
            'slug' => 'arreglos-mixtos',
            'description' => 'Combinaciones únicas de flores y decoraciones',
            'image' => 'mixtos',
            'is_active' => true,
        ]);

        $especiales = Category::create([
            'name' => 'Ocasiones Especiales',
            'slug' => 'ocasiones-especiales',
            'description' => 'Para San Valentín, cumpleaños, aniversarios y más',
            'image' => 'especiales',
            'is_active' => true,
        ]);

        // ==========================================
        // PRODUCTOS - ROSAS ETERNAS
        // ==========================================

        $p1 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Amor Amor - 15 Rosas',
            'slug' => 'ramo-amor-amor-15-rosas',
            'description' => 'Hermoso ramo de 15 rosas eternas rojas con luces LED, mariposas decorativas y envoltura premium. Incluye caja en forma de corazón.',
            'price' => 170.00,
            'sale_price' => null,
            'stock' => 10,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p1, [
            'ramo-amor-amor-1',
            'ramo-amor-amor-2',
        ]);

        $p2 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Amor Eterno - 25 Rosas',
            'slug' => 'ramo-amor-eterno-25-rosas',
            'description' => 'Espectacular ramo de 25 rosas eternas con corona dorada, mariposas, luces LED y envoltura de lujo. El regalo perfecto.',
            'price' => 400.00,
            'sale_price' => 350.00,
            'stock' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p2, [
            'ramo-amor-eterno-1',
            'ramo-amor-eterno-2',
            'ramo-amor-eterno-3',
        ]);

        $p3 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Eres Mi Sol - 20 Rosas',
            'slug' => 'ramo-eres-mi-sol-20-rosas',
            'description' => 'Ramo de 20 rosas eternas con girasol central, luces LED, mariposas y envoltura especial. Combina elegancia y alegría.',
            'price' => 280.00,
            'sale_price' => null,
            'stock' => 8,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p3, [
            'ramo-eres-mi-sol-1',
            'ramo-eres-mi-sol-2',
        ]);

        $p4 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Amor Bonito - 12 Rosas',
            'slug' => 'ramo-amor-bonito-12-rosas',
            'description' => 'Delicado ramo de 12 rosas eternas rosadas con luces LED, margaritas y envoltura elegante. Ideal para sorprender.',
            'price' => 150.00,
            'sale_price' => null,
            'stock' => 15,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p4, [
            'ramo-amor-bonito-1',
            'ramo-amor-bonito-2',
        ]);

        $p5 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Vida Mía - 18 Rosas',
            'slug' => 'ramo-vida-mia-18-rosas',
            'description' => 'Ramo de 18 rosas eternas rojas y rosadas, con corona decorativa, luces LED y envoltura premium.',
            'price' => 260.00,
            'sale_price' => null,
            'stock' => 7,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p5, [
            'ramo-vida-mia-1',
            'ramo-vida-mia-2',
        ]);

        $p6 = Product::create([
            'category_id' => $rosas->id,
            'name' => 'Ramo Rosas Negras - 15 Rosas',
            'slug' => 'ramo-rosas-negras-15-rosas',
            'description' => 'Elegante ramo de 15 rosas eternas negras con detalles morados, luces LED y envoltura oscura. Único y misterioso.',
            'price' => 200.00,
            'sale_price' => null,
            'stock' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p6, [
            'ramo-rosas-negras-1',
            'ramo-rosas-negras-2',
        ]);

        // ==========================================
        // PRODUCTOS - GIRASOLES
        // ==========================================

        $p7 = Product::create([
            'category_id' => $girasoles->id,
            'name' => 'Ramo 8 Girasoles Grandes',
            'slug' => 'ramo-8-girasoles-grandes',
            'description' => 'Hermoso ramo de 8 girasoles grandes con mariposas decorativas, luces LED y envoltura kraft. Alegría pura.',
            'price' => 180.00,
            'sale_price' => null,
            'stock' => 10,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p7, [
            'ramo-8-girasoles-1',
            'ramo-8-girasoles-2',
        ]);

        $p8 = Product::create([
            'category_id' => $girasoles->id,
            'name' => 'Ramo 12 Girasoles con Rosas',
            'slug' => 'ramo-12-girasoles-con-rosas',
            'description' => 'Combinación perfecta de 12 girasoles con rosas rojas, luces LED y envoltura especial. Sol y amor juntos.',
            'price' => 250.00,
            'sale_price' => 220.00,
            'stock' => 6,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p8, [
            'ramo-girasoles-rosas-1',
            'ramo-girasoles-rosas-2',
            'ramo-girasoles-rosas-3',
        ]);

        $p9 = Product::create([
            'category_id' => $girasoles->id,
            'name' => 'Mini Ramo 5 Girasoles',
            'slug' => 'mini-ramo-5-girasoles',
            'description' => 'Pequeño pero encantador ramo de 5 girasoles con luces LED. Perfecto para alegrar el día.',
            'price' => 100.00,
            'sale_price' => null,
            'stock' => 20,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p9, [
            'mini-ramo-girasoles-1',
        ]);

        // ==========================================
        // PRODUCTOS - MIXTOS
        // ==========================================

        $p10 = Product::create([
            'category_id' => $mixtos->id,
            'name' => 'Arreglo Jardín Encantado',
            'slug' => 'arreglo-jardin-encantado',
            'description' => 'Hermoso arreglo con rosas, girasoles, margaritas y follaje. Incluye luces LED y base decorativa.',
            'price' => 220.00,
            'sale_price' => null,
            'stock' => 8,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p10, [
            'arreglo-jardin-1',
            'arreglo-jardin-2',
        ]);

        $p11 = Product::create([
            'category_id' => $mixtos->id,
            'name' => 'Caja Corazón con Rosas',
            'slug' => 'caja-corazon-con-rosas',
            'description' => 'Elegante caja en forma de corazón con 12 rosas eternas, chocolates y tarjeta personalizada.',
            'price' => 180.00,
            'sale_price' => null,
            'stock' => 10,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p11, [
            'caja-corazon-1',
            'caja-corazon-2',
        ]);

        $p12 = Product::create([
            'category_id' => $mixtos->id,
            'name' => 'Arreglo Primavera',
            'slug' => 'arreglo-primavera',
            'description' => 'Colorido arreglo con tulipanes, rosas y margaritas. Perfecto para dar la bienvenida a la primavera.',
            'price' => 160.00,
            'sale_price' => 140.00,
            'stock' => 12,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p12, [
            'arreglo-primavera-1',
            'arreglo-primavera-2',
        ]);

        // ==========================================
        // PRODUCTOS - OCASIONES ESPECIALES
        // ==========================================

        $p13 = Product::create([
            'category_id' => $especiales->id,
            'name' => 'Set San Valentín Deluxe',
            'slug' => 'set-san-valentin-deluxe',
            'description' => 'Set completo: Ramo de 25 rosas, peluche, chocolates, globos y tarjeta. El regalo más romántico.',
            'price' => 500.00,
            'sale_price' => 450.00,
            'stock' => 3,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p13, [
            'set-san-valentin-1',
            'set-san-valentin-2',
            'set-san-valentin-3',
        ]);

        $p14 = Product::create([
            'category_id' => $especiales->id,
            'name' => 'Ramo Cumpleaños Feliz',
            'slug' => 'ramo-cumpleanos-feliz',
            'description' => 'Colorido ramo con globo de cumpleaños, 15 rosas de colores y luces LED. ¡A celebrar!',
            'price' => 200.00,
            'sale_price' => null,
            'stock' => 10,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p14, [
            'ramo-cumpleanos-1',
            'ramo-cumpleanos-2',
        ]);

        $p15 = Product::create([
            'category_id' => $especiales->id,
            'name' => 'Arreglo Día de la Madre',
            'slug' => 'arreglo-dia-de-la-madre',
            'description' => 'Especial arreglo con rosas rosadas, orquídeas y follaje premium. Para la reina del hogar.',
            'price' => 280.00,
            'sale_price' => null,
            'stock' => 8,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->createImages($p15, [
            'arreglo-madre-1',
            'arreglo-madre-2',
        ]);

        $p16 = Product::create([
            'category_id' => $especiales->id,
            'name' => 'Corona Fúnebre Elegante',
            'slug' => 'corona-funebre-elegante',
            'description' => 'Corona fúnebre con rosas blancas y follaje verde. Expresión de respeto y cariño.',
            'price' => 350.00,
            'sale_price' => null,
            'stock' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->createImages($p16, [
            'corona-funebre-1',
        ]);
    }

    /**
     * Crear imágenes para un producto
     */
    private function createImages(Product $product, array $publicIds): void
    {
        foreach ($publicIds as $index => $publicId) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $publicId,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }

}