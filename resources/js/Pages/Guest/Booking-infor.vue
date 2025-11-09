<template>
    <div class="booking-infor-section | padding-block-200">
        <div class="container">
            <div class="p-2">
                <Stepper value="2" class="basis-[50rem]">
                    <StepList>
                        <Step value="1" asChild>
                            <div class="flex flex-row flex-auto gap-2">
                                <button class="bg-transparent border-0 inline-flex gap-2 items-center">
                                    <span class="rounded-full border-1 w-8 h-8 inline-flex items-center justify-center">
                                        <i class="pi pi-check" />
                                    </span>
                                </button>
                                <span class="text-md">Room Selection</span>
                                <Divider />
                            </div>
                        </Step>
                        <Step value="2">Your information</Step>
                        <Step value="3">Confirm booking</Step>
                    </StepList>
                    <StepPanels>
                        <!-- <StepPanel v-slot="{ activateCallback }" value="1">
                            <div class="flex flex-col h-48">
                                <div
                                    class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">
                                    Content I</div>
                            </div>
                            <div class="flex pt-6 justify-end">
                                <Button label="Next" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('2')" />
                            </div>
                        </StepPanel> -->
                        <StepPanel v-slot="{ activateCallback }" value="2">
                            <div class="flex flex-col h-48">
                                <div
                                    class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">
                                    Content II</div>
                            </div>
                            <div class="flex p-2 justify-end">
                                <Button label="Next" icon="pi pi-arrow-right" iconPos="right"
                                    @click="activateCallback('3')" />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="3">
                            <div class="flex flex-col h-48">
                                <div
                                    class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">
                                    Content III</div>
                            </div>
                            <div class="pt-6">
                                <Button label="Back" severity="secondary" icon="pi pi-arrow-left"
                                    @click="activateCallback('2')" />
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from 'primevue/button';
import Divider from 'primevue/divider';

// stepper
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import StepItem from 'primevue/stepitem';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';

</script>