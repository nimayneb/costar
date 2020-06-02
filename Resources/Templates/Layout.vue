<template>
    <q-layout view="hHh lpR fFf" class="shadow-2 rounded-borders">
        <q-header elevated class="bg-primary text-white">
            <q-toolbar>
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">
                    <g>
                        <path
                                style="fill:transparent;fill-opacity:1;stroke:white;stroke-width:2;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
                                d="M 46.296296,51.906272 L 31.916351,42.474649 L 17.502712,51.8547 L 22.029072,
                                               35.264028 L 8.654054,24.454438 L 25.831443,23.632463 L 31.978866,7.5717174 L
                                               38.068716,23.65438 L 55.243051,24.537884 L 41.829396,35.299492 L 46.296296,
                                               51.906272 z"
                                transform="matrix(0.986858,0,0,1.03704,0.471316,1.159472)"
                        />
                    </g>
                </svg>

                <q-toolbar-title>
                    CoStaR - Coding Standard Ruler
                </q-toolbar-title>

                <q-btn color="primary" label="Settings">
                    <q-menu>
                        <q-list style="min-width: 300px">
                            <q-item>
                                <b>Settings</b>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-input filled v-model="this.data.name" label="Name"/>
                                </q-item-section>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-input
                                            v-model="this.data.description"
                                            label="Description"
                                            filled
                                            autogrow
                                    />
                                </q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>

                <q-btn color="primary" label="Export">
                    <q-menu>
                        <q-list style="min-width: 300px">
                            <q-item>
                                <b>Export</b>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-input filled v-model="this.exportFileName" label="File name"/>
                                </q-item-section>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-btn color="primary" @click="this.export">Save file</q-btn>
                                </q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>

                <q-btn color="primary" label="Import">
                    <q-menu>
                        <q-list style="min-width: 300px">
                            <q-item>
                                <b>Import</b>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-file filled bottom-slots v-model="this.importFile" label="Import">
                                        <template v-slot:before>
                                            <q-icon name="folder_open"/>
                                        </template>

                                        <template v-slot:hint>
                                            PMD file (phpcs.xml)
                                        </template>
                                    </q-file>
                                </q-item-section>
                            </q-item>

                            <q-item>
                                <q-item-section>
                                    <b>Available Files:</b>
                                </q-item-section>
                            </q-item>

                            <q-slide-item left-color="red" v-for="(item, index) in getLocalStorageItems()" @left="deleteEntry(index)" :key="item.name" clickable>
                                <template v-slot:left>
                                    <q-icon name="delete" />
                                </template>

                                <q-item>
                                    <q-item-section avatar>
                                        <q-icon name="folder_open"/>
                                    </q-item-section>

                                    <q-item-section>
                                        <div class="q-pa-md q-gutter-md">
                                            <div class="text-body1">
                                                {{ item.name }}
                                                <q-badge color="green"> Enable {{ enabledRules(item.rules) }}</q-badge>
                                            </div>
                                            <div class="subtitle1">
                                                {{ item.description }}
                                            </div>
                                        </div>
                                    </q-item-section>
                                </q-item>
                            </q-slide-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <q-page class="q-pa-md">
                <slot></slot>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<script>
    module.exports = {
        name: 'Layout',

        props: {
            data: Object
        },

        data() {
            return {
                importFile: {},
                exportFileName: 'phpcs.xml'
            }
        },

        methods: {
            deleteEntry(index) {
                localStorage.removeItem(localStorage.key(index))
            },

            enabledRules(rules) {
                let enables = 0

                rules.forEach((rule) => (rule.enable) ? enables++ : 0)

                return enables
            },

            * getLocalStorageItems() {
                for (let index = 0; index < localStorage.length; index++) {
                    yield JSON.parse(localStorage.getItem(localStorage.key(index)))
                }
            },

            forceFileDownload(fileName, response) {
                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')

                link.href = url
                link.setAttribute('download', fileName)

                document.body.appendChild(link)

                link.click()
                link.remove()
            },

            export() {
                localStorage.setItem(this.data.name, JSON.stringify(this.data))

                axios.post(
                        'http://127.0.0.1:8080/phpcs-export',
                        {
                            data: this.data,
                            responseType: 'arraybuffer'
                        }
                ).then(response => {
                    this.forceFileDownload('phpcs.xml', response);
                })
                        .catch(error => {
                        })
            },

            import() {

            },
        }
    }
</script>