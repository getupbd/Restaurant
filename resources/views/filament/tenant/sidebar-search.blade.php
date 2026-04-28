<div x-data="{
    search: '',
    searchOpen: false,
    isSidebarCollapsed() {
        const sidebar = document.querySelector('.fi-sidebar');
        return sidebar && !sidebar.classList.contains('fi-sidebar-open');
    },
    filter() {
        const query = this.search.toLowerCase();
        const groups = document.querySelectorAll('.fi-sidebar-group');
        
        groups.forEach(group => {
            const items = group.querySelectorAll('.fi-sidebar-item');
            let hasVisibleItem = false;
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const matches = text.includes(query);
                item.style.display = matches ? '' : 'none';
                if (matches) hasVisibleItem = true;
            });
            
            group.style.display = hasVisibleItem ? '' : 'none';
        });

        const ungroupedItems = document.querySelectorAll('.fi-sidebar-nav-groups > ul > .fi-sidebar-item');
        ungroupedItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(query);
            item.style.display = matches ? '' : 'none';
        });
    }
}"
    x-init="
        const sidebar = document.querySelector('.fi-sidebar');
        if (sidebar) {
            new MutationObserver(() => {
                if (isSidebarCollapsed()) { searchOpen = false; search = ''; filter(); }
            }).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        }
    "
    class="pb-2 pt-2"
    :class="isSidebarCollapsed() ? 'px-1 flex justify-center' : 'px-4'"
>
    {{-- Collapsed: icon only button --}}
    <template x-if="isSidebarCollapsed()">
        <div class="relative">
            <button
                @click="searchOpen = !searchOpen"
                class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-white/10 transition"
                title="Search Menu"
            >
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </button>
            {{-- Floating search popup --}}
            <div
                x-show="searchOpen"
                x-transition
                @click.away="searchOpen = false"
                class="absolute left-12 top-0 z-50 w-56 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-xl p-2"
            >
                <input
                    type="search"
                    x-model="search"
                    @input="filter()"
                    x-ref="collapsedInput"
                    x-effect="if (searchOpen) $nextTick(() => $refs.collapsedInput?.focus())"
                    placeholder="Search Menu..."
                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white px-3 py-2"
                />
            </div>
        </div>
    </template>

    {{-- Expanded: full search input --}}
    <template x-if="!isSidebarCollapsed()">
        <div class="relative w-full">
            <input 
                type="search" 
                x-model="search" 
                @input="filter()" 
                placeholder="Search Menu..." 
                class="w-full text-sm transition duration-75 border-gray-300 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:ring-inset disabled:opacity-70 dark:border-white/10 dark:bg-white/5 dark:text-white"
            />
            <svg class="absolute w-4 h-4 text-gray-400 top-2.5 right-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
    </template>
</div>
