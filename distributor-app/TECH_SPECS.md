# Tech Specs — SentraX DistributorApp

## UI/UX Styling Reference

### Sidebar Navigation

#### Simple Link (tanpa children)
```svelte
<a href={href}
  class="flex items-center rounded-lg transition
    {isActive(href) ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
>
  <Icon name={icon} class="shrink-0 h-5 w-5" />
  <span class="flex-1 text-left text-sm">{label}</span>
</a>
```

#### Parent with Children (Expanded)
```svelte
<button onclick={toggleExpand}
  class="flex w-full items-center rounded-lg transition
    {isExpanded ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
>
  <Icon name={icon} class="shrink-0 h-5 w-5" />
  <span class="flex-1 text-left text-sm">{label}</span>
  <svg class="h-4 w-4 transition {isExpanded ? 'rotate-180' : ''}">...</svg>
</button>

{#if isExpanded}
  <div class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
    {#each children as child}
      <a href={child.href}
        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition
          {isActive(child.href) ? 'bg-white/15 font-semibold text-white' : 'text-white/60 hover:bg-white/10 hover:text-white'}"
      >
        <span class="h-1.5 w-1.5 rounded-full {isActive(child.href) ? 'bg-white' : 'bg-white/40'}"></span>
        {child.label}
      </a>
    {/each}
  </div>
{/if}
```

#### Collapsed Dropdown (Icon Only)
```svelte
<!-- Parent Button -->
<button onclick={toggleDropdown}
  class="flex h-11 w-11 justify-center items-center rounded-lg transition
    {isDropdownOpen ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
>
  <Icon name={icon} class="h-6 w-6" />
</button>

<!-- Dropdown Panel -->
{#if isDropdownOpen}
  <div class="absolute left-full top-0 z-[100] ml-1 w-44 overflow-hidden rounded-lg bg-white shadow-lg">
    <div class="border-b border-slate-100 px-3 py-2">
      <p class="text-sm font-semibold text-slate-800">{parentLabel}</p>
    </div>
    {#each children as child}
      <a href={child.href}
        class="flex items-center gap-2 px-3 py-2.5 text-sm transition
          {isActive(child.href) ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-slate-50'}"
      >
        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0 {isActive(child.href) ? 'bg-primary-600' : 'bg-slate-300'}"></span>
        {child.label}
      </a>
    {/each}
  </div>
{/if}
```

### Dropdown Pattern (User Menu)
```svelte
<!-- Trigger -->
<button class="flex w-full items-center gap-2.5 rounded-lg p-1.5 transition hover:bg-white/10">
  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 text-xs font-bold text-white">{initials}</div>
</button>

<!-- Dropdown Panel -->
{#if isOpen}
  <div class="absolute bottom-full mb-2 overflow-hidden rounded-lg bg-white shadow-lg w-full">
    <button onclick={logout}
      class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50">
      <Icon name="logout" class="h-4 w-4" />
      Logout
    </button>
  </div>
{/if}
```

### Active State Classes
| State | Class |
|-------|-------|
| Sidebar link (active) | `bg-white/15 font-semibold text-white` |
| Sidebar link (hover) | `text-white/70 hover:bg-white/10 hover:text-white` |
| Dropdown item (active) | `bg-primary-50 font-semibold text-primary-700` |
| Dropdown item (hover) | `text-slate-600 hover:bg-slate-50` |
| Bullet indicator (active) | `bg-primary-600` atau `bg-white` |
| Bullet indicator (inactive) | `bg-slate-300` atau `bg-white/40` |

### Z-Index Scale
| Layer | Value | Usage |
|-------|-------|-------|
| Content | - | Default |
| Sidebar | `z-30` | Fixed sidebar |
| Modal Backdrop | `z-40` | Overlay |
| Dropdown | `z-[100]` | Sidebar collapsed dropdown |
| User Menu | - | Absolute positioned |

---

## Routes Structure

### Admin (Nested under Products)
```
/admin/products                    → Daftar Produk
/admin/products/categories         → Kategori
/admin/products?tab=prices        → Daftar Harga (tab, not separate page)
```

---

## Business Flow Reference

### Pricing
- CBP (Cost Bill of Production) = Harga/ton dari produsen
- Margin = Markup distributor
- Ongkir = Charge transport (optional, tergantung pola distribusi customer)
- CBP Final = Harga/ton + Margin (+ Ongkir jika FOT)

### Master Data Schema

#### Products (Flat)
```php
products: id, category_id, manufacturer_code, name, uom, weight_per_unit_kg, is_active
```

#### Categories
```php
categories: id, name, is_active
```

---

*Last updated: 2026-08-03*
