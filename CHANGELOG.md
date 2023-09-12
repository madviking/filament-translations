### 12.9.2023 - timo [ at ] east.fi

* Dependency to spatie/laravel-translation-loader is removed.
* Auto-create translations — when you introduce a new translation in your code, it will get automatically added to db (option in config)
* Google translate support
* Translate option for individual strings (using English as source language)
* Simplified scan saving
* Flush cache
* Database structure is changed, each language version is now a separate row, making it easier to export data for manual translation
* Filters for language, group and empty translations