<template>
    <div class="app">
        <AppHeader fixed>
            <SidebarToggler class="d-lg-none" display="md" mobile/>
            <b-link class="navbar-brand" to="#">
                <img class="navbar-brand-full" src="image/brand/web_logo.png" width="auto" height="35" alt="web Logo">
                <img class="navbar-brand-minimized" src="image/brand/web_logo.png" width="auto" height="30"
                     alt="web Logo">
            </b-link>
            <SidebarToggler class="d-md-down-none" display="lg"/>
            <b-navbar-nav class="ml-auto">
                <DefaultHeaderDropdownIntro class="px-3 d-md-down-none"/>
                <b-nav-item class="px-3 d-md-down-none" href="/about-manganese/">{{ $t("headers.about-manganese") }}</b-nav-item>
                <b-nav-item class="px-3 d-md-down-none" href="/membership/">{{ $t("headers.membership") }}</b-nav-item>
                <DefaultHeaderDropdownActiv class="px-3 d-md-down-none"/>
                <b-nav-item class="px-3 d-md-down-none" href="/contact-us/">{{ $t("headers.contact") }}</b-nav-item>
                <DefaultHeaderDropdownAccnt/>
                <DefaultHeaderDropdownTrans/>
            </b-navbar-nav>
        </AppHeader>
        <div class="app-body">
            <AppSidebar fixed>
                <SidebarHeader/>
                <SidebarForm/>
                <SidebarNav :navItems="navItems"></SidebarNav>
                <SidebarFooter/>
                <SidebarMinimizer/>
            </AppSidebar>
            <main class="main">
                <Breadcrumb :list="list"/>
                <div class="container-fluid">
                    <div class="row col" v-if="isHome">
                        <h1>Welcome to web</h1>
                    </div>
                    <router-view></router-view>
                </div>
            </main>
            <AppAside fixed>
                <!--aside-->
            </AppAside>
        </div>
        <TheFooter>
            <!--footer-->
            <div>
                <a href="#"></a>
                <span class="ml-1"></span>
                </div>
                <div class="ml-auto">
                    <span class="mr-1"></span>
                    <a href="#">Web</a>
                </div>
            </TheFooter>
        </div>
    </template>

    <script>
        import {
            Header as AppHeader,
            SidebarToggler,
            Sidebar as AppSidebar,
            SidebarFooter,
            SidebarForm,
            SidebarHeader,
            SidebarMinimizer,
            SidebarNav,
            Aside as AppAside,
            AsideToggler,
            Footer as TheFooter,
            Breadcrumb
        } from '@coreui/vue';
        import DefaultHeaderDropdownAccnt from './DefaultHeaderDropdownAccnt';
        import DefaultHeaderDropdownIntro from './DefaultHeaderDropdownIntro';
        import DefaultHeaderDropdownActiv from './DefaultHeaderDropdownActiv';
        import DefaultHeaderDropdownTrans from './DefaultHeaderDropdownTrans';

        export default {
            name: 'DefaultContainer',
            components: {
                AsideToggler,
                AppHeader,
                AppSidebar,
                AppAside,
                TheFooter,
                Breadcrumb,
                SidebarForm,
                SidebarFooter,
                SidebarToggler,
                SidebarHeader,
                SidebarNav,
                SidebarMinimizer,
                DefaultHeaderDropdownAccnt,
                DefaultHeaderDropdownIntro,
                DefaultHeaderDropdownActiv,
                DefaultHeaderDropdownTrans
            },
            props: ['isHome'],
            data() {
                return {
                    navNames : {},
                    navItems: []
                }
            },
            computed: {
                name() {
                    return this.$route.name
                },
                list() {
                    return this.$route.matched.filter((route) => route.name || route.meta.label)
                },
                locale() {
                    return this.$i18n.locale;
                }
            },
            created() {
                this.navNames = this.getNavNames();
                this.navItems = this.getNavs(this.navNames);
            },
            methods: {
                getNavNames() {
                    const names = {
                        home: this.$t("navs.home"),
                        user: this.$t("navs.user"),
                        reports: this.$t("navs.reports"),
                        dataSubmission: this.$t("navs.data-submission"),
                        exportData: this.$t("navs.export-data"),
                        createGroup: this.$t("navs.create-group"),
                        importData: this.$t("navs.import-data"),
                        onlineDatabase: this.$t("navs.online-database"),
                        addNewUser: this.$t("navs.add-new-user")
                    }
                    return names;
                },
                getNavs(navNames) {
                    let navItems = [
                        {
                            name: navNames.home,
                            url: '/home',
                            icon: 'icon-home',
                            meta: {
                                hasRole: ['ADMIN', 'SUPER-USER', 'USER']
                            }
                        },
                        {
                            name: navNames.user,
                            url: '/manage-user',
                            icon: 'icon-user',
                            meta: {
                                hasRole: ['ADMIN']
                            }
                        },
                        {
                            name: navNames.reports,
                            url: '/reports',
                            icon: 'icon-list',
                            meta: {
                                hasRole: ['ADMIN', 'SUPER-USER', 'USER']
                            }
                        },
                        {
                            name: navNames.dataSubmission,
                            url: '/data-submission',
                            icon: 'icon-basket-loaded',
                            meta: {
                                hasRole: ['ADMIN', 'SUPER-USER']
                            }
                        },
                        {
                            name: navNames.exportData,
                            url: '/export-data',
                            icon: 'icon-cloud-download',
                            meta: {
                                hasRole: ['ADMIN']
                            }
                        },
                        {
                            name: navNames.createGroup,
                            url: '/groups',
                            icon: 'icon-layers',
                            meta: {
                                hasRole: ['ADMIN']
                            }
                        },
                        {
                            name: navNames.importData,
                            url: '/import-data',
                            icon: 'icon-cloud-upload',
                            meta: {
                                hasRole: ['ADMIN']
                            }
                        },
                        {
                            name: navNames.onlineDatabase,
                            url: '/online-database',
                            icon: 'icon-list',
                            meta: {
                                hasRole: ['ADMIN', 'SUPER-USER', 'USER']
                            }
                        },
                        {
                            name: navNames.addNewUser,
                            url: '/create-user',
                            icon: 'icon-user',
                            meta: {
                                hasRole: ['ADMIN']
                            }
                        },
                    ];

                    const isAdmin = this.$store.getters['security/hasRole']('ADMIN');
                    const isSuperUser = this.$store.getters['security/hasRole']('SUPER-USER');
                    const isUser = this.$store.getters['security/hasRole']('USER');

                    if (isUser) {
                        navItems = navItems.filter((item) => {
                            return item.meta && item.meta.hasRole.indexOf('USER') > -1;
                        });
                    }

                    if (isSuperUser) {
                        navItems = navItems.filter((item) => {
                            return item.meta && item.meta.hasRole.indexOf('SUPER-USER') > -1;
                        });
                    }

                    if (isAdmin) {
                        navItems = navItems.filter((item) => {
                            return item.meta && item.meta.hasRole.indexOf('ADMIN') > -1;
                        });
                    }

                    return navItems;
                }
            },
            watch: {
                locale(newVal, oldVal) {
                    this.navNames = this.getNavNames();
                    this.navItems = this.getNavs(this.navNames);
                }
            }
        }
    </script>
