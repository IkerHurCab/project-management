<script setup>
    import { ref, computed } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import StandardButton from '@/Components/StandardButton.vue';
    import InputWithIcon from '@/Components/InputWithIcon.vue';
    
    const props = defineProps({
      isOpen: Boolean,
      allMembers: Array,
      searchQuery: String,
    });
    
    const emit = defineEmits(['close', 'add-members', 'update-search-query']);
    
    const form = useForm({
      selectedMembers: [],
    });
    
    const searchQuery = ref(props.searchQuery || '');
    
    const filteredMembers = computed(() => {
      return props.allMembers.filter(member => 
        member.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });
    
    const toggleMemberSelection = (member) => {
      const index = form.selectedMembers.findIndex(m => m.id === member.id);
      if (index > -1) {
        form.selectedMembers.splice(index, 1);
      } else {
        form.selectedMembers.push(member);
      }
    };
    
    const isMemberSelected = (member) => {
      return form.selectedMembers.some(m => m.id === member.id);
    };
    
    const submitForm = () => {
      emit('add-members', form.selectedMembers);
      form.reset();
      emit('close');
    };
    
    const handleInputChange = (event) => {
      searchQuery.value = event.target.value;
      emit('update-search-query', searchQuery.value);
    };
    
    const removeMember = (member) => {
      const index = form.selectedMembers.findIndex(m => m.id === member.id);
      if (index > -1) {
        form.selectedMembers.splice(index, 1);
      }
    };
    </script>
    
    <template>
      <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-950 rounded-lg w-full max-w-md h-auto border border-gray-700 shadow-lg">
          <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center bg-gray-950 rounded-t-lg">
            <h2 class="text-2xl font-semibold text-white">Add Members</h2>
            <button @click="emit('close')" class="text-gray-400 cursor-pointer hover:text-white transition-colors">
              <box-icon name='x' color='currentColor'></box-icon>
            </button>
          </div>
          <div class="p-6 space-y-6">
            <div>
              <InputWithIcon 
                icon="search" 
                :value="searchQuery"
                @input="handleInputChange"
                placeholder="Search members..." 
                class="h-10 w-full"
                type="text" 
              />
            </div>
            
            <!-- Selected Members Cards -->
            <div v-if="form.selectedMembers.length > 0" class="space-y-2">
              <h3 class="text-lg font-semibold text-white">Selected Members</h3>
              <div class="grid overflow-y-auto max-h-20 grid-cols-2 gap-2">
                <div v-for="member in form.selectedMembers" :key="member.id" 
                     class="bg-gray-800 rounded-lg p-2 flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      {{ member.name.charAt(0) }}
                    </div>
                    <span class="text-white text-sm">{{ member.name }}</span>
                  </div>
                  <button @click="removeMember(member)" class="text-red-500 hover:text-red-700">
                    <box-icon name='x' size="sm" color='currentColor'></box-icon>
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Member List -->
            <div class="overflow-y-auto max-h-60 scrollbar">
              <div 
                v-for="member in filteredMembers" 
                :key="member.id"
                @click="toggleMemberSelection(member)"
                class="flex cursor-pointer items-center justify-between p-2 hover:bg-gray-700 rounded-lg transition-colors"
              >
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white">
                    {{ member.name.charAt(0) }}
                  </div>
                  <span class="text-white">{{ member.name }}</span>
                </div>
                <span class="text-gray-400 text-sm">{{ member.role }}</span>
                <div v-if="isMemberSelected(member)" class="text-green-500">
                  <box-icon name='check' color='currentColor'></box-icon>
                </div>
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <StandardButton type="button" @click="emit('close')" class="bg-gray-600 hover:bg-gray-500 transition-colors">
                Cancel
              </StandardButton>
              <StandardButton @click="submitForm" class="bg-blue-600 hover:bg-blue-500 transition-colors">
                Add Members
              </StandardButton>
            </div>
          </div>
        </div>
      </div>
    </template>