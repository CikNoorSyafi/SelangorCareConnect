<template>
    <div class="w-full relative">
        <input type="text" v-model="search" placeholder="Location" class="border p-3 rounded w-full" />

        <ul v-if="filteredLocations.length">
            <li v-for="(location, index) in filteredLocations" :key="index">
                {{ location }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            search: "",
            showDropdown: false,
            locations: [
                "Sepang",
                "Hulu Langat",
                "Hulu Selangor",
                "Gombak",
                "Petaling",
                "Klang",
                "Kuala Langat",
                "Kuala Selangor",
                "Sabak Bernam",
                "Seri Kembangan",
                "Puchong",
                "Shah Alam",
                "Subang Jaya"
            ],
            filteredLocations: []
        };
    },
    methods: {
        filterLocations() {
            const keyword = this.search.toLowerCase();
            this.filteredLocations = this.locations.filter(loc =>
                loc.toLowerCase().includes(keyword)
            );
        },
        selectLocation(location) {
            this.search = location;
            this.showDropdown = false;
            this.$emit("selected", location);
        },
        hideDropdown() {
            setTimeout(() => {
                this.showDropdown = false;
            }, 150);
        }
    }
};
</script>