<script setup>
import { useDisplay } from "vuetify";
import logo from "@images/logo.svg";

const props = defineProps({
  tag: {
    type: [String, null],
    required: false,
    default: "aside",
  },
  isOverlayNavActive: {
    type: Boolean,
    required: true,
  },
  toggleIsOverlayNavActive: {
    type: Function,
    required: true,
  },
});

const { mdAndDown } = useDisplay();
const refNav = ref();
const route = useRoute();

watch(
  () => route.path,
  () => {
    props.toggleIsOverlayNavActive(false);
  },
);

const isVerticalNavScrolled = ref(false);

const updateIsVerticalNavScrolled = val =>
  (isVerticalNavScrolled.value = val);

const handleNavScroll = evt => {
  isVerticalNavScrolled.value = evt.target.scrollTop > 0;
};
</script>

<template>
  <Component :is="props.tag" ref="refNav" class="layout-vertical-nav" :class="[
    {
      visible: isOverlayNavActive,
      scrolled: isVerticalNavScrolled,
      'overlay-nav': mdAndDown,
    },
  ]">
    <!-- 👉 Header -->
    <div class="nav-header">
      <slot name="nav-header">
        <RouterLink to="/" class="app-logo d-flex align-center gap-x-3 app-title-wrapper">
          <img class="d-flex" :src="logo" />

          <h1 class="leading-normal">
            <Font style="color: #008cb5">Melati</Font>
          </h1>
        </RouterLink>
      </slot>
    </div>
    <slot name="before-nav-items">
      <div class="vertical-nav-items-shadow" />
    </slot>
    <slot name="nav-items" :update-is-vertical-nav-scrolled="updateIsVerticalNavScrolled">
      <slot />
    </slot>

    <slot name="after-nav-items" />
  </Component>
</template>

<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/mixins";

// 👉 Vertical Nav
.layout-vertical-nav {
  position: fixed;
  z-index: variables.$layout-vertical-nav-z-index;
  display: flex;
  flex-direction: column;
  block-size: 100%;
  inline-size: variables.$layout-vertical-nav-width;
  inset-block-start: 0;
  inset-inline-start: 0;
  transition: transform 0.25s ease-in-out, inline-size 0.25s ease-in-out,
    box-shadow 0.25s ease-in-out;
  will-change: transform, inline-size;

  .nav-header {
    display: flex;
    align-items: center;

    .header-action {
      cursor: pointer;
    }
  }

  .app-title-wrapper {
    margin-inline-end: auto;
  }

  .nav-items {
    block-size: 100%;

    // ℹ️ We no loner needs this overflow styles as perfect scrollbar applies it
    // overflow-x: hidden;

    // // ℹ️ We used `overflow-y` instead of `overflow` to mitigate overflow x. Revert back if any issue found.
    overflow-y: auto;
  }

  .nav-item-title {
    overflow: hidden;
    margin-inline-end: auto;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  // 👉 Collapsed
  .layout-vertical-nav-collapsed & {
    &:not(.hovered) {
      inline-size: variables.$layout-vertical-nav-collapsed-width;
    }
  }

  // 👉 Overlay nav
  &.overlay-nav {
    &:not(.visible) {
      transform: translateX(-#{variables.$layout-vertical-nav-width});

      @include mixins.rtl {
        transform: translateX(variables.$layout-vertical-nav-width);
      }
    }
  }
}
</style>
