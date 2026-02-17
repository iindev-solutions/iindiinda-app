<template>
  <div class="taxi-map">
    <div class="map-placeholder">
      <span class="map-icon">🗺️</span>
      <p v-if="from && to" class="map-text">
        Маршрут: {{ from }} → {{ to }}
      </p>
      <p v-else class="map-text">
        Укажите адреса для отображения маршрута
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  from?: string
  to?: string
}

defineProps<Props>()
</script>

<style scoped>
.taxi-map {
  width: 100%;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
}

.map-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.map-icon {
  font-size: 48px;
}

.map-text {
  color: white;
  font-size: 14px;
  text-align: center;
  padding: 0 20px;
}
</style>
