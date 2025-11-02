<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import deudores from '@/routes/deudores';
import agendas from '@/routes/agendas';
import debtors from '@/routes/debtors';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, HandCoins, LayoutGrid, CalendarCheck, CalendarClock, FileText, FileChartColumnIncreasing } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Inst. Financieras',
        href: deudores.index(),
        icon: FileText,
    },
    {
        title: 'Inst. Privadas',
        href: deudores.index(),
        icon: FileText,
    },
    {
        title: 'Inst. Públicas',
        href: deudores.index(),
        icon: FileText,
    },
    {
        title: 'Agenda de Pagos',
        href: agendas.index(),
        icon: CalendarCheck,
    },
    {
        title: 'Agenda de Gestión',
        href: agendas.index(),
        icon: CalendarClock,
    },
    {
        title: 'Power BI',
        href: dashboard(),
        icon: FileChartColumnIncreasing,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
