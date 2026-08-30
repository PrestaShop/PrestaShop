# Service Containers & Kernels

> Cross-cutting guide. PrestaShop 9 runs **four** distinct service containers. Which one serves a request — and which
> service-definition files it loads — determines whether a given service (e.g. `validator`, `translator`) even exists.
> Getting this wrong is easy and the failures are confusing (a service "missing" in one context, a 500 only in BO, …).

## The four containers

| Container | Class / built by | Serves | FrameworkBundle? |
|-----------|------------------|--------|------------------|
| **AdminKernel** | `app/AdminKernel.php` (Symfony kernel) | Back office — **including legacy `index.php?controller=Admin*`** (it boots the kernel, then dispatches/redirects) | ✅ full |
| **FrontKernel** | `app/FrontKernel.php` (Symfony kernel) | Front-office Symfony routes | ✅ full |
| **AdminAPIKernel** | `app/AdminAPIKernel.php` (Symfony kernel) | Admin API (`/admin-api/*`) | ✅ full |
| **FO legacy** | `src/Adapter/ContainerBuilder.php` → `LegacyContainerBuilder` (hand-built) | Front-office **legacy** dispatch (`FrontController`) + a compile-time bootstrap bridge (see gotchas) | ❌ none |

The three Symfony kernels all extend `app/AppKernel.php`; each loads `app/config/<appId>/config_<env>.yml` (appId =
`admin` / `front` / `admin-api`). They get `validator`, `translator`, the serializer, the full Context services and all
`validator.constraint_validator`-tagged validators from FrameworkBundle + the bundle config glob.

The **FO legacy container is NOT a Symfony/FrameworkBundle container** — it is assembled by hand. Anything Symfony
normally provides (Doctrine, `validator`, `translator`, full Context) is **absent unless explicitly wired**, via:
- `ContainerBuilderExtensionInterface` implementations run before compile — `DoctrineBuilderExtension` (Doctrine),
  `ValidatorBuilderExtension` (the `validator`, see [Component/ExtraProperty](Component/ExtraProperty/CONTEXT.md));
- the YAML it loads (below).
`ContainerBuilder::getContainer('admin')` **throws** — the BO never uses this container directly.

## Service-definition file split (where to put a service)

| Files | Loaded by | Scope |
|-------|-----------|-------|
| `src/PrestaShopBundle/Resources/config/services/**` (bundle) | The **SF kernels** load the full tree. The **FO legacy container loads only a hand-picked subset** — the files explicitly listed in the `imports:` of `config/services/common.yml` (by convention the `common.yml` of each subfolder, e.g. `core/common.yml`, `bundle/common.yml`, `extra_property/common.yml`). It is **not** an automatic glob; most files here are SF-kernel-only. | **SF kernels**; FO only for the explicitly-imported files |
| `config/services/common.yml` | FO legacy container only (imported by the per-context entry below) | **FO-only**, shared across FO entries |
| `config/services/{front,webservice}/services_<env>.yml` | FO legacy container, per entry point (`front`, `webservice`) | FO/legacy entry-specific |
| `app/config/{admin,front,admin-api}/{config,services}_<env>.yml` | One Symfony kernel each | **Per-kernel (SF only)** |

**Rules of thumb:**
- A service in a **bundle** config is loaded into **every SF kernel**. It is available in the **FO legacy container
  only if its file is explicitly imported by `config/services/common.yml`** (the convention: put FO-needed
  definitions in a `common.yml` and add it to that import list). Once a bundle service IS imported into the FO
  container, **its dependencies must resolve there too** — do **not** hard-depend on a FrameworkBundle-only service
  (e.g. `@validator`, `@translator`) from such a service unless that dependency is also provided in the FO container
  (e.g. wired by a `ContainerBuilderExtensionInterface`).
- **FO-only** service definitions belong in `config/services/*` — never in bundle configs the SF kernels load (that
  would leak them into / conflict with the SF kernels).
- **SF-kernel-only** definitions belong under `app/config/<kernel>/`.

## Gotchas

- **BO uses the full container.** Even legacy `AdminXxxController` runs under AdminKernel. So a service is fully
  available in BO. The "lightweight" FO legacy container is **front-office only**.
- **…but the FO legacy container is also built transiently during *any* kernel's container compilation.**
  `LegacyHookSubscriber::getSubscribedEvents()` (read by `RegisterListenersPass` at compile time, before any kernel
  container exists) calls `Hook::getHookModuleFilter()`, which — finding `SymfonyContainer::getInstance()` null at that
  moment — falls back to building the FO legacy container to read hook-module data. **Consequence:** a service that
  **fails to compile in the FO container breaks the boot of *every* kernel, including BO.** (This is exactly what a
  hard `@validator` dependency on an ExtraProperty service did before `ValidatorBuilderExtension` provided a FO
  `validator`.) Do not "fix" this by guarding the fallback on front-office context — it legitimately runs at
  compile time in all contexts, where `isFrontOfficeContext()` is false.
- **`ObjectModel::findContainer()`** resolves through `ContainerFinder` (`Context->container` → controller container →
  `SymfonyContainer::getInstance()`) and returns `null` if none — it has **no** hard FO-container fallback. So
  ObjectModel features (e.g. extra-property validation) use whichever container is resolved; ensure the services they
  need exist in all of them.
- A FrameworkBundle service id like `validator` does **not** exist in the FO legacy container by default; reference it
  only where it is guaranteed (SF kernels) or where it has been hand-wired (FO via `ValidatorBuilderExtension`).

## Related
- [Component/ExtraProperty/CONTEXT.md](Component/ExtraProperty/CONTEXT.md) — the validator is wired into the FO container by `ValidatorBuilderExtension` (graceful per-constraint skip for validators whose deps are FO-absent).
- [MULTISTORE.md](MULTISTORE.md) — Context/ShopConstraint resolution.
- [GOTCHAS.md](GOTCHAS.md) — other cross-cutting traps.
