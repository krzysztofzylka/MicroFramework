const { ref } = Vue
export default {
    setup() {
        const count = ref(0)
        return { count }
    },
    template: `<div>count is {{ count }}</div>`
}