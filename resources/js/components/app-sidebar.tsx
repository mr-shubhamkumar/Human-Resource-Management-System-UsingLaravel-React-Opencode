import { Link } from '@inertiajs/react';
import {
    BookOpen,
    BriefcaseBusiness,
    Building2,
    CalendarCheck,
    CalendarDays,
    FileText,
    FolderGit2,
    LayoutGrid,
    Users,
    Wallet,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
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
import { index as attendancesIndex } from '@/routes/attendances';
import { index as departmentsIndex } from '@/routes/departments';
import { index as employeesIndex } from '@/routes/employees';
import { index as leavesIndex } from '@/routes/leaves';
import { index as payrollsIndex } from '@/routes/payrolls';
import { index as payslipsIndex } from '@/routes/payslips';
import { index as positionsIndex } from '@/routes/positions';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Employees',
        href: employeesIndex(),
        icon: Users,
    },
    {
        title: 'Departments',
        href: departmentsIndex(),
        icon: Building2,
    },
    {
        title: 'Positions',
        href: positionsIndex(),
        icon: BriefcaseBusiness,
    },
    {
        title: 'Attendance',
        href: attendancesIndex(),
        icon: CalendarCheck,
    },
    {
        title: 'Leaves',
        href: leavesIndex(),
        icon: CalendarDays,
    },
    {
        title: 'Payrolls',
        href: payrollsIndex(),
        icon: Wallet,
    },
    {
        title: 'Payslips',
        href: payslipsIndex(),
        icon: FileText,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
